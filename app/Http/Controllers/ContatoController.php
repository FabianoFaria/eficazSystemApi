<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use EllipseSynergie\ApiResponse\Contracts\Response;
use App\Contato;
use App\Transformer\ContatoTransformer;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class ContatoController extends Controller
{

    protected $respose;


    public function __construct(Contato $contato)
    {
        $this->contato = $contato;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        //Get all task
        //$contatos = Contato::paginate(15);
        $contatos = Contato::all();
        $contatos =Contato::where('Tipo_Pessoa', 25)
               ->orderBy('Nome', 'asc')
               ->take(10)
               ->get();
        // Return a collection of $task with pagination
        //return $this->response->withPaginator($contatos, new  ContatoTransformer());
        return $contatos;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return "Bem vindo ao metódo create contato!";

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
        //dd($request->all());

        if( ! $this->contato->isValid($input = $request->all())){


            return response()->json($this->contato->errors, 400);

            //return Redirect::back()->withInput()->withErrors($this->contato->errors);

        }else{


            $this->contato->Nome                    = $request->get('Nome');
            $this->contato->Nome_Fantasia           = $request->get('Nome_Fantasia');
            $this->contato->Email                   = $request->get('Email');
            $this->contato->cpf_cnpj                = $request->get('Cpf_Cnpj');
            $this->contato->Data_Nascimento         = $request->get('Data_Nascimento');

            $this->contato->Origem_ID               = 1375;
            $this->contato->Parceiro_Origem_ID      = $request->get('parceiro_sistema');

            //Dados que terão dados configurados por padrão
            if(strlen($request->get('Cpf_Cnpj')) == 11){
                //Contato é uma pessoa física
                $this->contato->Tipo_Pessoa         = 24;
            }else{
                //Contato é uma pessoa juridica
                $this->contato->Tipo_Pessoa         = 25;
            } 

            $this->contato->Codigo                  = '';
            $this->contato->Sexo                    = '';
            $this->contato->Grupo_ID                = 0;
            $this->contato->Centro_Custo_ID         = 0;
            $this->contato->Tipo_Cadastro           = 'a:1:{i:0;s:3:"153";}';
            $this->contato->Tipo_Vinculo            = 'a:1:{i:0;s:3:"101";}';
            $this->contato->Observacao              = 'Cliente vindo do sistema de indicações da Eficaz.';
            $this->contato->Usuario_Cadastro_ID     = 1089;
            $this->contato->Usuario_Alteracao_ID    = 1089;
            $this->contato->Tabela_Preco_ID         = 0;
            $this->contato->Areas_Atuacoes          = 'N;';
            $this->contato->Regional_ID             = 1131;
            $this->contato->Cargo_ID                = 0;
            $this->contato->Empresa                 = 0;
            $this->contato->Situacao_ID             = 1;
            $this->contato->Ultimo_Login            = date('Y-m-d h:i:s');

            $this->contato->save();

            //$insertedId = $this->contato->Cadastro_ID;

            //dd($insertedId);

            return response()->json($this->contato, 201);

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //
        //$indicacao      = Contato::find($id);

        $id_parceiro    =  $request->get('parceiro_sistema');

        $indicacao      = DB::table('cadastros_dados')
                                ->select(
                                    'cadastros_dados.Cadastro_ID',
                                    'cadastros_dados.Nome',
                                    'cadastros_dados.Nome_Fantasia',
                                    'cadastros_dados.Data_Nascimento',
                                    'cadastros_dados.Cpf_Cnpj',
                                    'cadastros_dados.RG',
                                    'cadastros_dados.Email',
                                    'cadastros_dados.Foto',
                                    'cadastros_dados.Parceiro_Origem_ID'
                                )
                                ->where(
                                    [
                                        ['cadastros_dados.Cadastro_ID','=', $id],
                                        ['cadastros_dados.Parceiro_Origem_ID','=', $id_parceiro]
                                    ]
                                )
                                ->get();

        if(empty($indicacao)){  
            return response()->json(null, 200);
        }else{
            return response()->json($indicacao, 200);
        }
    }


    /**
     * Retorna a liste de clientes que foram indicados pelo parceiro.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function listarIndicacoesParceiros($id)
    {
        //
        $indicacoes     = DB::table('cadastros_dados')
                                ->select(
                                    'cadastros_dados.Cadastro_ID',
                                    'cadastros_dados.Nome',
                                    'cadastros_dados.Nome_Fantasia',
                                    'cadastros_dados.Cpf_Cnpj',
                                    'cadastros_dados.Email',
                                    'cadastros_dados.Data_Inclusao'
                                )
                                ->where([
                                        ['cadastros_dados.Parceiro_Origem_ID','=', $id]
                                    ])
                                ->get();

        if(empty($indicacoes)){  
            return response()->json(null, 200);
        }else{
            return response()->json($indicacoes, 200);
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
        $rules = array(
            'Nome'              => 'required',
            'Email'             => 'required|email',
            'Data_Nascimento'   => 'required|date_format:Y-m-d'
        );

        // Retirado o CPF/CNPJ de campos obrigatorios 
        // 'Cpf_Cnpj'          => 'required'

        $validator = Validator::make($request->all(), $rules);

        

        // $validation = Validator::make(
        //     array(
        //         'Nome'              => $request->get('Nome'),
        //         'Email'             => $request->get('Email'),
        //         'Data_Nascimento'   => $request->get('Data_Nascimento'),
        //         'Cpf_Cnpj'          => $request->get( 'Cpf_Cnpj' )
        //     ),
        //     array(
        //         'Nome'              => array( 'required' ),
        //         'Email'             => array( 'required', 'email' ),
        //         'Data_Nascimento'   => array( 'required', 'date_format:Y-m-d' ),
        //         'Cpf_Cnpj'          => array( 'required' )
        //     )
        // );

        // if ( $validation->fails() ) {
        //     $errors = $validation->messages();

        //     dd($errors);
        // }




        if( $validator->fails()){

            //return Redirect::back()->withInput()->withErrors($this->contato->errors);
            return response()->json($this->contato->errors, 400);

        }else{

            

            //dd($validator);

            $contatos = Contato::find($id);


            // $this->contato->Cadastro_ID     =  Contato::find($request->get('Cadastro_ID'));
            $contatos->Nome            = $request->get('Nome');
            $contatos->Nome_Fantasia   = $request->get('Nome_Fantasia');
            $contatos->Email           = $request->get('Email');
            $contatos->cpf_cnpj        = $request->get('Cpf_Cnpj');

             //Dados que terão dados configurados por padrão
            if(strlen($request->get('Cpf_Cnpj')) == 11){
                //Contato é uma pessoa física
                $contatos->Tipo_Pessoa         = 24;
            }else{
                //Contato é uma pessoa juridica
                $contatos->Tipo_Pessoa         = 25;
            } 
            
            $contatos->Data_Nascimento = $request->get('Data_Nascimento');

            $contatos->save();

            return response()->json($contatos, 201);

        }

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
