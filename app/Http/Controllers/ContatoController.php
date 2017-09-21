<?php

namespace App\Http\Controllers;

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


            $this->contato->Nome = $request->get('Nome');
            $this->contato->Nome_Fantasia = $request->get('Nome_Fantasia');
            $this->contato->Email = $request->get('Email');
            $this->contato->cpf_cnpj = $request->get('Cpf_Cnpj');
            $this->contato->Data_Nascimento = $request->get('Data_Nascimento');

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
    public function show($id)
    {
        //
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
    public function update(Request $request, $id ='')
    {
        //

        $input = $request->all();
        // $rules = array(
        //     'Nome'              => 'required',
        //     'Email'             => 'required|email',
        //     'Data_Nascimento'   => 'required|date_format:Y-m-d',
        //     'Cpf_Cnpj'          => 'required',
        // );

        //dd($request->all());

        // $validator = Validator::make($request->all(), $rules);

        // if( $validator->fails()){

        //     //return Redirect::back()->withInput()->withErrors($this->contato->errors);
        //     return response()->json($this->contato->errors, 400);

        // }else{

            $contatos = Contato::find($request->get('Cadastro_ID'));

            dd($input);

            $contatos->save();


            // $this->contato->Cadastro_ID     =  Contato::find($request->get('Cadastro_ID'));
            // $this->contato->Nome            = $request->get('Nome');
            // $this->contato->Nome_Fantasia   = $request->get('Nome_Fantasia');
            // $this->contato->Email           = $request->get('Email');
            // $this->contato->cpf_cnpj        = $request->get('Cpf_Cnpj');
            // $this->contato->Data_Nascimento = $request->get('Data_Nascimento');

            // $this->contato->save();

            return response()->json($this->contatos, 201);

        //}

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
