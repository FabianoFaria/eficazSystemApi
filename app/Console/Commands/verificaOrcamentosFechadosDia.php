<?php

namespace App\Console\Commands;

use DB;
use App\Orcamento;
use App\Orcamento_follow;
use App\Orcamentos_propostas;
use App\Orcamentos_propostas_produtos;
use App\Orcamentos_propostas_vencimentos;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

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
                                ->select(
                                    'ow.Titulo AS Orc_titulo',
                                    'ow.Workflow_ID',
                                    'ow.Data_Finalizado',
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
                                    ['ow.Data_Finalizado','<', '2017-10-30 23:59:59'],
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
                                    'tpPgm.Descr_Tipo'
                                )
                                ->get();

        if(!empty($totalOrcamento)){

            $i = 0; 

            foreach ($totalOrcamento as $orcamento) {
                
                $this->line('Orcamento ID '.$orcamento->Workflow_ID.' Total orcamento : '.$orcamento->Valor_Total_Proposta.' Data finalizado: '.$orcamento->Data_Finalizado.'');

                $i++;
                //$this->line('Total de orçamentos : '.$orcamento->Orc_titulo.'');


            }


            $this->info('Todos os orcamentos foram carregados com sucesso!');

        }else{

            $this->info('Nenhum orçamento fechado hoje foi encontrado!');

        }


        
    }
}
