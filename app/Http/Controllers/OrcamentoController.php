<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use EllipseSynergie\ApiResponse\Contracts\Response;
use App\Orcamento;
use App\Orcamento_follow;
use App\Orcamentos_propostas;
use App\Orcamentos_propostas_produtos;
use App\Orcamentos_propostas_vencimentos;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class OrcamentoController extends Controller
{   
    protected $respose;

    public function __construct(Orcamento $Orcamento)
    {
        $this->Orcamento = $Orcamento;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if( ! $this->Orcamento->isValid($input = $request->all())){


            return response()->json($this->Orcamento->errors, 400);

            //return Redirect::back()->withInput()->withErrors($this->contato->errors);

        }else{

            $this->Orcamento->Solicitante_ID    = $request->get('Cadastro_ID');
            $this->Orcamento->Titulo            = $request->get('Titulo');

            $this->Orcamento->Empresa_ID            = 1089;
            $this->Orcamento->Representante_ID      = 1089;
            $this->Orcamento->Situacao_ID           = 110;
            $this->Orcamento->Codigo                = '';
            $this->Orcamento->Data_Abertura         = date('Y-m-d h:i:s');
            $this->Orcamento->Data_Finalizado       = null;
            $this->Orcamento->Data_Cadastro         = date('Y-m-d h:i:s');
            $this->Orcamento->Usuario_Cadastro_ID   = 1089;

            $this->Orcamento->save();

            $orçamentoId                            = $this->Orcamento->Workflow_ID;

            //Salvar a descrição do orçamento
            $orcamento_follow                       = new Orcamento_follow();

            $orcamento_follow->Workflow_ID          = $orçamentoId;
            $orcamento_follow->Descricao            = $request->get('Descricao');

            $orcamento_follow->Dados                = '';
            $orcamento_follow->Situacao_ID          = 110;
            $orcamento_follow->Data_Cadastro        = date('Y-m-d h:i:s');
            $orcamento_follow->Usuario_Cadastro_ID  = 1089;

            $orcamento_follow->save();

            return response()->json($this->Orcamento, 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Recupera o status de um orçamento especifico

        $statusOrcamento    = DB::table('orcamentos_workflows')
                                ->join('tipo', 'orcamentos_workflows.Situacao_ID', '=', 'tipo.Tipo_ID')
                                ->join('cadastros_dados','cadastros_dados.Cadastro_ID','=','orcamentos_workflows.Solicitante_ID')
                                ->select('orcamentos_workflows.Empresa_ID', 
                                    'orcamentos_workflows.Solicitante_ID', 
                                    'orcamentos_workflows.Representante_ID',
                                    'orcamentos_workflows.Situacao_ID',
                                    'orcamentos_workflows.Codigo',
                                    'orcamentos_workflows.Titulo',
                                    'orcamentos_workflows.Data_Abertura',
                                    'orcamentos_workflows.Data_Finalizado',
                                    'orcamentos_workflows.Data_Cadastro',
                                    'orcamentos_workflows.Usuario_Cadastro_ID',
                                    'tipo.Descr_Tipo as Situacao',
                                    'cadastros_dados.Nome'
                                )
                                ->where('Workflow_ID', '=', $id)
                                ->first();

        //dd($statusOrcamento);

        if(empty($statusOrcamento)){
            return response()->json(null, 200);
        }else{
            return response()->json($statusOrcamento, 200);
        }

        /*
            Select w.Empresa_ID,
                             w.Solicitante_ID, 
                             w.Representante_ID, 
                             w.Situacao_ID, 
                             w.Codigo, 
                             w.Titulo, 
                             w.Data_Abertura, 
                             w.Data_Finalizado,
                            w.Data_Cadastro, w.Usuario_Cadastro_ID, s.Descr_Tipo as Situacao
                    from orcamentos_workflows w
                    left join tipo s on s.Tipo_ID = w.Situacao_ID
                    where Workflow_ID = '259'
            
        */
    }


    /**
     * Carrega os dados detalhados do orçamento especificado
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function orcamentoDetalhado($id)
    {
        //
        // $statusOrcamento    = DB::table('orcamentos_workflows')
        //                         ->leftJoin('orcamentos_propostas', 'orcamentos_workflows.Workflow_ID', '=', 'orcamentos_propostas.Workflow_ID')
        //                         ->leftJoin('tipo', 'tipo.Tipo_ID', '=', 'orcamentos_workflows.Situacao_ID')
        //                         ->leftJoin('orcamentos_propostas_produtos', 'orcamentos_propostas_produtos.Proposta_ID', '=', 'orcamentos_propostas.Proposta_ID')
        //                         ->select('orcamentos_workflows.Workflow_ID',
        //                             'orcamentos_workflows.Titulo as Orc_titulo',
        //                             'orcamentos_propostas.Proposta_ID',
        //                             'orcamentos_propostas.Titulo',
        //                             'tipo.Descr_Tipo as Status',
        //                             'orcamentos_workflows.Data_Finalizado',
        //                             DB::raw('sum((orcamentos_propostas_produtos.Quantidade * orcamentos_propostas_produtos.Valor_Venda_Unitario)) as totalServico')
        //                         )
        //                         ->where([
        //                             ['orcamentos_workflows.Workflow_ID', '=', $id],
        //                             ['orcamentos_propostas.Situacao_ID', '=', '1'],
        //                             ['orcamentos_propostas_produtos.Situacao_ID', '=', '1'],
        //                             ['orcamentos_workflows.Situacao_ID', '=', '113'],
        //                         ])
        //                         ->groupBy('orcamentos_propostas.Proposta_ID') 
        //                         ->first();

        $statusOrcamento    = DB::table('orcamentos_propostas_vencimentos')
                                ->leftJoin('orcamentos_propostas', 'orcamentos_propostas_vencimentos.Proposta_ID', '=','orcamentos_propostas.Proposta_ID')
                                ->leftJoin('orcamentos_workflows', 'orcamentos_propostas.Workflow_ID','=','orcamentos_workflows.Workflow_ID')
                                ->leftJoin('orcamentos_propostas_produtos','orcamentos_propostas_produtos.Proposta_ID','=','orcamentos_propostas.Proposta_ID')
                                ->leftJoin('tipo','tipo.Tipo_ID','=','orcamentos_workflows.Situacao_ID')
                                ->leftJoin('tipo as tpPgm','tpPgm.Tipo_ID','=','orcamentos_propostas.Forma_Pagamento_ID')
                                ->select(
                                    'orcamentos_workflows.Workflow_ID',
                                    'orcamentos_workflows.Titulo',
                                    'orcamentos_workflows.Data_Finalizado',
                                    'orcamentos_propostas.Proposta_ID',
                                    'orcamentos_propostas.Forma_Pagamento_ID',
                                    'orcamentos_propostas_vencimentos.Data_Vencimento',
                                    'orcamentos_propostas_vencimentos.Dias_Vencimento',
                                    'orcamentos_propostas_vencimentos.Valor_Vencimento',
                                    'orcamentos_propostas_vencimentos.Valor_Vencimento as totalServico',
                                    'tipo.Descr_Tipo as Status',
                                    'tpPgm.Descr_Tipo as tipoPagamento'
                                )
                                ->where([
                                        ['orcamentos_workflows.Workflow_ID','=', $id],
                                        ['orcamentos_workflows.Situacao_ID','=','113'],
                                        ['orcamentos_propostas.Situacao_ID','=','1'],
                                        ['orcamentos_propostas_produtos.Situacao_ID','=','1']
                                    ])
                                ->groupBy(
                                        'orcamentos_workflows.Workflow_ID',
                                        'orcamentos_workflows.Titulo',
                                        'orcamentos_workflows.Data_Finalizado',
                                        'orcamentos_propostas.Proposta_ID',
                                        'orcamentos_propostas.Forma_Pagamento_ID',
                                        'orcamentos_propostas_vencimentos.Data_Vencimento',
                                        'orcamentos_propostas_vencimentos.Dias_Vencimento',
                                        'orcamentos_propostas_vencimentos.Valor_Vencimento',
                                        'tipo.Descr_Tipo',
                                        'tpPgm.Descr_Tipo'
                                        ) 
                                ->first();
        /*
            
            select 
                ow.Titulo,
                op.Proposta_ID,
                op.Forma_Pagamento_ID,
                opv.Data_Vencimento,
                opv.Dias_Vencimento,
                opv.Valor_Vencimento
            from orcamentos_propostas_vencimentos opv
            left join orcamentos_propostas op on opv.Proposta_ID = op.Proposta_ID
            left join orcamentos_workflows ow on op.Workflow_ID = ow.Workflow_ID
            left join orcamentos_propostas_produtos opp on opp.Proposta_ID = op.Proposta_ID
            where ow.Workflow_ID = '280' and op.Situacao_ID = 1 and opp.Situacao_ID = 1
            group by ow.Titulo, op.Proposta_ID, opv.Data_Vencimento, opv.Dias_Vencimento, opv.Valor_Vencimento

                
        */


        if(empty($statusOrcamento)){  
            return response()->json(null, 200);
        }else{
            return response()->json($statusOrcamento, 200);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function statusOrcamentosParceiro($id)
    {
        // 
        $statusOrcamentos   = '';

        // $statusOrcamentos   = DB::table('orcamentos_propostas_vencimentos')
        //                          ->where([
        //                                 ['cadastros_dados.Parceiro_Origem_ID','=', $id],
        //                                 ['orcamentos_workflows.Situacao_ID','=','113'],
        //                                 ['orcamentos_propostas.Situacao_ID','=','1'],
        //                                 ['orcamentos_propostas_produtos.Situacao_ID','=','1']
        //                             ]);

        if(empty($statusOrcamentos)){  
            return response()->json(null, 200);
        }else{
            return response()->json($statusOrcamentos, 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function listarOrcamentosCliente($id)
    {

        //
        $listaOrcamento     = DB::table('orcamentos_propostas_vencimentos')
                                ->leftJoin('orcamentos_propostas', 'orcamentos_propostas_vencimentos.Proposta_ID', '=','orcamentos_propostas.Proposta_ID')
                                ->leftJoin('orcamentos_workflows', 'orcamentos_propostas.Workflow_ID','=','orcamentos_workflows.Workflow_ID')
                                ->leftJoin('orcamentos_propostas_produtos','orcamentos_propostas_produtos.Proposta_ID','=','orcamentos_propostas.Proposta_ID')
                                ->leftJoin('tipo','tipo.Tipo_ID','=','orcamentos_workflows.Situacao_ID')
                                ->leftJoin('tipo as tpPgm','tpPgm.Tipo_ID','=','orcamentos_propostas.Forma_Pagamento_ID')
                                ->select(
                                    'orcamentos_workflows.Workflow_ID',
                                    'orcamentos_workflows.Titulo',
                                    'orcamentos_workflows.Data_Finalizado',
                                    'orcamentos_workflows.Data_Abertura',
                                    'orcamentos_propostas.Proposta_ID',
                                    'orcamentos_propostas.Forma_Pagamento_ID',
                                    'orcamentos_propostas_vencimentos.Data_Vencimento',
                                    'orcamentos_propostas_vencimentos.Dias_Vencimento',
                                    'orcamentos_propostas_vencimentos.Valor_Vencimento',
                                    'orcamentos_propostas_vencimentos.Valor_Vencimento as totalServico',
                                    'tipo.Descr_Tipo as Status',
                                    'tpPgm.Descr_Tipo as tipoPagamento'
                                )
                                ->where([
                                        ['orcamentos_workflows.Solicitante_ID','=', $id],
                                        ['orcamentos_propostas.Situacao_ID','=','1'],
                                        ['orcamentos_propostas_produtos.Situacao_ID','=','1'],
                                        ['orcamentos_propostas_vencimentos.Situacao_ID','=','1']
                                    ])
                                ->groupBy(
                                        'orcamentos_workflows.Workflow_ID',
                                        'orcamentos_workflows.Titulo',
                                        'orcamentos_workflows.Data_Finalizado',
                                        'orcamentos_workflows.Data_Abertura',
                                        'orcamentos_propostas.Proposta_ID',
                                        'orcamentos_propostas.Forma_Pagamento_ID',
                                        'orcamentos_propostas_vencimentos.Data_Vencimento',
                                        'orcamentos_propostas_vencimentos.Dias_Vencimento',
                                        'orcamentos_propostas_vencimentos.Valor_Vencimento',
                                        'tipo.Descr_Tipo',
                                        'tpPgm.Descr_Tipo'
                                        ) 
                                ->get();

        if(empty($listaOrcamento)){  
            return response()->json(null, 200);
        }else{
            return response()->json($listaOrcamento, 200);
        }


    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Retorna o total de orçamentos em aberto por um determinado cliente
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function totalOrcamentosParceiro($id_parceiro)
    {
        // 


        /*
            SELECT
            COUNT(ow.Titulo)
            FROM orcamentos_workflows ow
            LEFT JOIN cadastros_dados cd on cd.Cadastro_ID = ow.Solicitante_ID
            WHERE cd.Parceiro_Origem_ID =1 AND ow.Situacao_ID != '113'
    
        */
        $totalOrcamento = DB::table('orcamentos_workflows AS ow')
                            ->leftJoin('cadastros_dados AS cd', 'cd.Cadastro_ID', '=','ow.Solicitante_ID')
                            ->select(DB::raw('count(ow.Titulo) as totalOrcamentos'))
                            ->where([
                                ['cd.Parceiro_Origem_ID','=', $id_parceiro],
                                ['ow.Situacao_ID','!=','113']
                            ])
                            ->get();

        if(empty($totalOrcamento)){  
            return response()->json(null, 200);
        }else{
            return response()->json($totalOrcamento[0], 200);
        }
    }
}
