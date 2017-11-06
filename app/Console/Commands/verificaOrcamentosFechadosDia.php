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

        //DB::table('users')->where('active', 0)->delete();
        $hoje = date('Y-m-d');

        $totalOrcamento     = DB::table('orcamentos_propostas AS op')
                                ->join('orcamentos_workflows AS ow', 'ow.Workflow_ID', '=','op.Workflow_ID')
                                ->leftJoin('orcamentos_propostas_produtos AS opp', 'opp.Proposta_ID', '=','op.Proposta_ID')
                                ->leftJoin('orcamentos_propostas_vencimentos AS opv', 'opv.Proposta_ID', '=','op.Proposta_ID')
                                ->leftJoin('tipo','tipo.Tipo_ID','=','ow.Situacao_ID')
                                ->leftJoin('tipo AS tpPgm','tpPgm.Tipo_ID','=','op.Forma_Pagamento_ID')
                                ->leftJoin('cadastros_dados AS cd', 'cd.Cadastro_ID', '=', 'ow.Solicitante_ID')
                                ->select(
                                    'ow.Titulo AS Orc_titulo',
                                    'ow.Workflow_ID',
                                    'ow.Data_Finalizado',
                                    'cd.Nome',
                                    'cd.Nome_Fantasia',
                                    'cd.Parceiro_Origem_ID',
                                    'op.Titulo',
                                    'op.Proposta_ID',
                                    DB::raw('SUM(opp.Quantidade) AS Quantidade_Total_Proposta'),
                                    DB::raw('SUM(opp.Quantidade * opp.Valor_Venda_Unitario) AS Valor_Total_Proposta'),
                                    DB::raw('count(opp.Proposta_Produto_ID) AS Total_Itens_Proposta'),
                                    'opv.Data_Vencimento',
                                    'opv.Dias_Vencimento',
                                    'opv.Valor_Vencimento',
                                    'tipo.Descr_Tipo AS Status',
                                    'tpPgm.Descr_Tipo AS tipoPagamento'
                                )
                                ->where([
                                    ['ow.Data_Finalizado','>', '2017-10-01 00:00:00'],
                                    ['ow.Data_Finalizado','<', '2017-10-31 23:59:59'],
                                    ['ow.Situacao_ID','=','113'],
                                    ['op.Situacao_ID','=','1'],
                                    ['opp.Situacao_ID','=','1'],
                                    ['opv.Situacao_ID','=','1']
                                ])
                                ->groupBy(
                                    'ow.Workflow_ID',
                                    'ow.Data_Finalizado',
                                    'ow.Titulo',
                                    'op.Titulo',
                                    'op.Proposta_ID',
                                    'opv.Data_Vencimento',
                                    'opv.Valor_Vencimento',
                                    'opv.Dias_Vencimento',
                                    'tipo.Descr_Tipo',
                                    'tpPgm.Descr_Tipo',
                                    'cd.Nome',
                                    'cd.Nome_Fantasia',
                                    'cd.Parceiro_Origem_ID'
                                )
                                ->get();

        if(!empty($totalOrcamento)){


            foreach ($totalOrcamento as $orcamento) {
                
                $this->line('Orcamento ID '.$orcamento->Nome.' Total orcamento : '.$orcamento->Valor_Total_Proposta.' Data finalizado: '.$orcamento->Data_Finalizado.'');

                //$this->line('Total de orçamentos : '.$orcamento->Orc_titulo.'');


                //Verifica se o orcamento do cliente tem relação com algum parceiro, e então irá carregar os dados do mesmo
                if($orcamento->Parceiro_Origem_ID != 0){

                    $this->line('-- Procurando dados do parceiro...');

                    $valorCommicao  = Orcamento::comissaoOrcamentoAulso($orcamento->Valor_Total_Proposta);

                    $parceiro = null;
                    
                    //$client = new Client(); //GuzzleHttp\Client
                    $client = new Client();  

                    try{

                        $r = $client->get('https://parcerias.eficazsystem.com.br/dadosParceiro/'.$orcamento->Parceiro_Origem_ID);

                        $statusRequisicao   = $r->getStatusCode();
                        //$resultado          = $r->json();
                        $resultado          = json_decode($r->getBody());

                        /*

                            $response = $client->get(
                                'https://www.waytwo.com/api/v1/otp/',
                                [
                                    'query' => [
                                        'app_id' => '9e04999b6924bd',
                                        'access_token' => 'c493389e67bd7e1',
                                        'mobile' => ''
                                    ]
                                ]
                            )->json();


                        */

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

                //Verificando data para faturamento do orçamento
                $diasParaFaturarTemp    = $orcamento->Dias_Vencimento;
                $diasParaPagarParceiro  = $orcamento->Dias_Vencimento + 5;
                
                $dateTempFaturar        = strtotime($orcamento->Data_Finalizado." +".$diasParaFaturarTemp."days");
                $dateTempPagarParceiro  = strtotime($orcamento->Data_Finalizado." +".$diasParaPagarParceiro."days");

                $dataFaturamento        = date("Y-m-d H:i:s", $dateTempFaturar);
                $dataPagamentoParceiro  = date("Y-m-d H:i:s", Orcamento::verificaPagamentoFimDeSemana($dateTempPagarParceiro));

                //$this->info('Data para faturar orçamento  : '. $dataFaturamento);
                //$this->info('Data para pagar parceiro     : '. $dataPagamentoParceiro);

                $data = array(
                        'nomeCliente'            => $orcamento->Nome,
                        'nomeFantasiaCliente'    => $orcamento->Nome_Fantasia,
                        'idOrcamento'            => $orcamento->Workflow_ID,
                        'tituloOrcamento'        => $orcamento->Orc_titulo,
                        'valorTotalOrcamento'    => $orcamento->Valor_Total_Proposta,
                        'statusOrcamento'        => $orcamento->Status,
                        'tipoPagamento'          => $orcamento->tipoPagamento,
                        'dataVencimento'         => $dataFaturamento,
                        'dataVencimentoParceiro' => $dataPagamentoParceiro,
                        'dadosParceiro'          => $dadosParceiro
                            
                    );

                //Teste de envio de email para parceiro recem cadastrado
                // Mail::send('emails.aviso_faturamento', $data, function($message)
                // {
                //     // Endereço de envio de aviso de orçamentos definido via hardcoded
                //     // Implementar uma forma de configurar endereço de email via sistema.
                //     if($orcamento->Nome != ''){

                //         $nomeCliente = $orcamento->Nome;
                //     }else{
                //         $nomeCliente = $orcamento->Nome_Fantasia;
                //     }

                //     $message->to('sistemaeficaz@sistema.eficazsystem.com.br', 'Financeiro')
                //             ->from('noreply@sistema.eficazsystem.com.br')
                //             ->subject('Orçamentos fechados EficazSystem,'.$nomeCliente.' !');

                // });


                /*
                 * Envio de email para Laravel 5.3
                 */

                Mail::to('sistemaeficaz@sistema.eficazsystem.com.br')->send(new OrcamentosFechadosMailable($data));

            }


            $this->info('Todos os orcamentos foram carregados com sucesso!');

        }else{

            $this->info('Nenhum orçamento fechado hoje foi encontrado!');

        }


        
    }
}
