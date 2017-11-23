<?php

namespace App\Console\Commands;

use DB;
use Mail;
use App\Mail\OrcamentosFechadosMailable;
use App\Orcamento;
use App\Orcamento_follow;
use App\Orcamentos_propostas;
use App\Orcamentos_propostas_produtos;
use App\Orcamentos_propostas_vencimentos;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class verificaOrcamentosFechadosDia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:verificaOrcamentosFechadosDia';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando efetua a verificacao dos orcamentos fechados no dia, enviando um email avisando o financeiro sobre as informacoes do orcamentos e dos possiveis parceiros.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this->line("Listando os orçamentos que foram fechados hoje, aguarde um instante...");
        // $this->line("Some text");
        // $this->info("Hey, watch this !");
        // $this->comment("Just a comment passing by");
        // $this->question("Why did you do that?");
        // $this->error("Ops, that should not happen.");

        //$hoje = date('Y-m-d h');
        $hoje = date('Y-m-d H', strtotime('-1 hour'));

        $totalOrcamento = DB::table('orcamentos_workflows AS ow')
                                ->leftJoin('cadastros_dados AS cd', 'cd.Cadastro_ID', '=', 'ow.Solicitante_ID')
                                ->leftJoin('tipo','tipo.Tipo_ID','=','ow.Situacao_ID')
                                ->select(
                                    'ow.Titulo AS Orc_titulo',
                                    'ow.Workflow_ID',
                                    'ow.Data_Finalizado',
                                    'cd.Nome',
                                    'cd.Nome_Fantasia',
                                    'cd.Parceiro_Origem_ID',
                                    'tipo.Descr_Tipo AS Status'
                                )
                                ->where([
                                    ['ow.Data_Finalizado','>=', $hoje.':00:00'],
                                    ['ow.Data_Finalizado','<=', $hoje.':59:59'],
                                    ['ow.Situacao_ID','=','113']
                                ])
                                ->groupBy(
                                    'ow.Workflow_ID',
                                    'ow.Titulo',
                                    'ow.Data_Finalizado',
                                    'cd.Nome',
                                    'cd.Nome_Fantasia',
                                    'cd.Parceiro_Origem_ID',
                                    'tipo.Descr_Tipo'
                                )
                                ->get();

        /*
          $this->line($totalOrcamento);

            dd($totalOrcamento);  
        */



        if(!empty($totalOrcamento)){

            foreach ($totalOrcamento as $orcamento) {

                $diasParaFaturarTemp    = 30;
                $diasParaPagarParceiro  = $diasParaFaturarTemp + 5;
                $totalProposta          = 0;
                $formaPagamento         = '';
                
                $this->line('Orcamento ID '.$orcamento->Workflow_ID.' Cliente_nome : '.$orcamento->Nome.' Data finalizado: '.$orcamento->Data_Finalizado.' ');

                $this->info('Carregando as propostas do orçamento!');

                #######################################
                #  Carrega as propostas do orçamento  #
                #######################################

                $propostasOrcamento = DB::table('orcamentos_propostas AS op')
                                            ->leftJoin('tipo AS tpPgm','tpPgm.Tipo_ID','=','op.Forma_Pagamento_ID')
                                            ->select(
                                                'op.Titulo',
                                                'op.Proposta_ID',
                                                'tpPgm.Descr_Tipo AS tipoPagamento'
                                            )
                                            ->where([
                                                ['op.Workflow_ID','=', $orcamento->Workflow_ID],
                                                ['op.Situacao_ID','=','1'],
                                                ['op.Status_ID','=','141']
                                            ])
                                            ->get();
                //dd($propostasOrcamento);

                if(!empty($propostasOrcamento)){

                    #########################################
                    # Carrega e soma os valores da proposta #
                    #########################################

                    // $totalProposta = 0;

                    foreach ($propostasOrcamento as $proposta) {

                        $formaPagamento = $proposta->tipoPagamento;

                        $this->info('ID da proposta   : '. $proposta->Proposta_ID .' Titulo proposta : '.$proposta->Titulo.' Tipo pagamento : '.$proposta->tipoPagamento);

                        // CALCULA O VALOR DOS PRODUTOS DE CADA PROPOSTA

                        $produtosProposta = DB::table('orcamentos_propostas_produtos AS opp')
                                                ->select(
                                                    'Proposta_Produto_ID',
                                                    'Quantidade',
                                                    'Valor_Venda_Unitario',
                                                    DB::raw('SUM(opp.Quantidade) AS Quantidade_Total_Proposta'),
                                                    DB::raw('SUM(opp.Quantidade * opp.Valor_Venda_Unitario) AS Valor_Total_Proposta'),
                                                    DB::raw('count(opp.Proposta_Produto_ID) AS Total_Itens_Proposta')
                                                )
                                                ->where([
                                                    ['opp.Proposta_ID','=', $proposta->Proposta_ID],
                                                    ['opp.Situacao_ID','=','1']
                                                ])
                                                ->groupBy(
                                                    'Proposta_Produto_ID',
                                                    'Quantidade',
                                                    'Valor_Venda_Unitario'
                                                )
                                                ->get();
                        
                        if(!empty($produtosProposta)){

                            foreach ($produtosProposta as $produtos) {

                                # $this->info('ID de produto   : '. $produtos->Proposta_Produto_ID .' Quantidade : '.$produtos->Quantidade.' Valor_total_proposta :'.$produtos->Valor_Total_Proposta);

                                $totalProposta = $totalProposta + $produtos->Valor_Total_Proposta;

                            }

                        }

                        ###########################################
                        #   Carrega formas de pagamento e valores #
                        ###########################################

                        $this->info('Carregando os valores e prazos...');

                        $prazosValoresProposta = DB::table('orcamentos_propostas_vencimentos AS opv')
                                            ->select(
                                                'opv.Data_Vencimento',
                                                'opv.Dias_Vencimento',
                                                'opv.Valor_Vencimento'
                                            )
                                            ->where([
                                                ['opv.Proposta_ID','=', $proposta->Proposta_ID],
                                                ['opv.Situacao_ID','=','1']
                                            ])
                                            ->get();

                        if(!empty($prazosValoresProposta)){

                            foreach ($prazosValoresProposta as $prazos) {

                                $this->info('Datas para vencimento : '. $prazos->Data_Vencimento .' Dias para pagar :'.$prazos->Dias_Vencimento.' Valor do venciamento :'. $prazos->Valor_Vencimento);

                                $diasParaFaturarTemp    = $prazos->Dias_Vencimento;
                                $diasParaPagarParceiro  = $prazos->Dias_Vencimento + 5;
                            }



                        }else{

                            //Caso não tenha sido definido prazos para faturamento
                            $diasParaFaturarTemp    = 30;
                            $diasParaPagarParceiro  = $diasParaFaturarTemp + 5;

                        }

                        ###########################################################
                        # Ajusta a data de faturamento e de pagamento do parceiro #
                        ###########################################################
                        $dateTempFaturar        = strtotime($orcamento->Data_Finalizado." + ".$diasParaFaturarTemp."days");
                        $dateTempPagarParceiro  = strtotime($orcamento->Data_Finalizado." + ".$diasParaPagarParceiro."days");

                        $dataFaturamento        = date("Y-m-d H:i:s", $dateTempFaturar);
                        $dataPagamentoParceiro  = date("Y-m-d H:i:s", Orcamento::verificaPagamentoFimDeSemana($dateTempPagarParceiro));

                        //Data de fechamento do orçamento
                        $dataFechamentoOrc      = $orcamento->Data_Finalizado;
                        $testeDataOrc           = explode(' ',$dataFechamentoOrc);
                        $dataFechamento         = implode('/',array_reverse(explode('-', $testeDataOrc[0]))); 

                        //Corrige o formato da data de faturamento
                        $data                   = $dataFaturamento;
                        $teste                  = explode(' ',$data); 
                        $dataFaturamento        = implode('/',array_reverse(explode('-', $teste[0])));

                        //Corrige o formato da data de pagamento do parceiro
                        $dataTemp                   = $dataPagamentoParceiro;
                        $testeTemp                  = explode(' ',$dataTemp); 
                        $dataPagamentoParceiro      = implode('/',array_reverse(explode('-', $testeTemp[0])));

                        $this->info('Data para faturar orçamento  : '. $dataFaturamento);
                        $this->info('Data para pagar parceiro     : '. $dataPagamentoParceiro);

                        # Total dos produtos
                        $this->info('ID da proposta   : '. $proposta->Proposta_ID .' Total dos produtos da proposta :'.$totalProposta);

                    }


                    #######################################
                    #  Carrega os dados do parceiro       #
                    #######################################
                    if($orcamento->Parceiro_Origem_ID != 0){

                        $this->line('-- Procurando dados do parceiro...');

                        $valorCommicao  = Orcamento::comissaoOrcamentoAulso($totalProposta);

                        $parceiro = null;

                        //$client = new Client(); //GuzzleHttp\Client
                        $client = new Client();

                        try{

                            $r = $client->get('https://parcerias.eficazsystem.com.br/dadosParceiro/'.$orcamento->Parceiro_Origem_ID);

                            $statusRequisicao   = $r->getStatusCode();
                            //$resultado          = $r->json();
                            $resultado          = json_decode($r->getBody());

                        }catch(RequestException $e){

                             // To catch exactly error 400 use 
                            if ($e->getResponse()->getStatusCode() == '400') {
                                //echo "Got response 400";
                                Session::flash('error_cad', 'Não foi possivel encontrar os dados do parceiro, faveor tentar novamente em alguns instantes.');

                                $parceiro = null;
                            }
                        }

                        switch ($statusRequisicao) {

                            case '201':

                                $parceiro = $resultado;

                            break;

                            case '400':
                
                                $parceiro = null;

                            break;

                            default:

                                $parceiro = null;

                            break;
                        }

                        //dd($parceiro->parceiro);
                        foreach ( $parceiro->parceiro as $parceiroConta )
                        {  

                            //$this->line('Parceiro para receber commisao: '.$parceiroConta['nome_vendedor'].' email do parceiro : '.$parceiroConta['email_usuario'].' Valor da comissao: '.$valorCommicao);

                            $dadosParceiro  = array(
                                    'idParceiroSistema'         => $parceiroConta->id_parceiro_sistema,
                                    'nomeParceiro'              => $parceiroConta->nome_vendedor,
                                    'emailParceiro'             => $parceiroConta->email_usuario,
                                    'nome_conta'                => $parceiroConta->nome_conta,
                                    'agencia'                   => $parceiroConta->agencia,
                                    'numero_conta'              => $parceiroConta->numero_conta,
                                    'nome_instituicao_bancaria' => $parceiroConta->nome_instituicao_bancaria,
                                    'valorCommicao'             => $valorCommicao
                                );

                        }

                    }else{

                        $dadosParceiro = null;
                    }

                    $data = array(
                        'nomeCliente'            => $orcamento->Nome,
                        'nomeFantasiaCliente'    => $orcamento->Nome_Fantasia,
                        'idOrcamento'            => $orcamento->Workflow_ID,
                        'tituloOrcamento'        => $orcamento->Orc_titulo,
                        'valorTotalOrcamento'    => $totalProposta,
                        'statusOrcamento'        => $orcamento->Status,
                        'tipoPagamento'          => $formaPagamento,
                        'dataFechamentoOrc'      => $dataFechamento,
                        'dataVencimento'         => $dataFaturamento,
                        'dataVencimentoParceiro' => $dataPagamentoParceiro,
                        'dadosParceiro'          => $dadosParceiro,
                        'idProposta'             => $proposta->Proposta_ID,
                        'tituloProposta'         => $proposta->Titulo                      
                    );

                    if($data['nomeCliente'] != ''){

                        $nomeCliente    = $data['nomeCliente'];
                    }else{
                        $nomeCliente    = $data['nomeFantasiaCliente'];
                    }

                    $dadosCliente       = ['dadosVendedor' => $data, 'nomeCliente' => $nomeCliente];


                    // //Teste de envio de email para parceiro recem cadastrado
                    Mail::send('emails.aviso_faturamento', $dadosCliente, function($message) use ($dadosCliente)
                    {
                        // Endereço de envio de aviso de orçamentos definido via hardcoded
                        // Implementar uma forma de configurar endereço de email via sistema.
                        //$message->to('sistemaeficaz@sistema.eficazsystem.com.br', 'finaceiro')
                        //$message->to('sistemaeficaz@sistema.eficazsystem.com.br', 'Teste')

                        // MENSAGEM FINAL
                        $message->to('sabine.trech@eficazsystem.com.br', 'finaceiro')
                                ->from('noreply@sistema.eficazsystem.com.br')
                                ->subject('Orçamentos fechados EficazSystem,'.$dadosCliente['nomeCliente'].' !')
                                ->cc('sistemaeficaz@sistema.eficazsystem.com.br', 'manutenção')
                                ->cc('operador03@eficazsystem.com.br', 'Atendimento')
                                ->cc('fernanda.trech@eficazsystem.com.br', 'Atendiemtno');
                     

                        // FIM DO EMAIL
                    });

                }


                /*
                 * Envio de email para Laravel 5.3
                 */

                //Mail::to('sistemaeficaz@sistema.eficazsystem.com.br')->send(new OrcamentosFechadosMailable($data));

            }


            $this->info('Todos os orcamentos foram carregados com sucesso!');

        }else{

            $this->info('Nenhum orçamento fechado hoje foi encontrado!');

        }


        
    }
}
