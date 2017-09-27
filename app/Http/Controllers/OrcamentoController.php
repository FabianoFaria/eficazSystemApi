<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use EllipseSynergie\ApiResponse\Contracts\Response;
use App\Orcamento;
use App\Orcamento_follow;
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
                                    'tipo.Descr_Tipo as Situacao'
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
}