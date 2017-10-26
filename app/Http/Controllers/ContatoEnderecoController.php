<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use EllipseSynergie\ApiResponse\Contracts\Response;
use App\ContatoEndereco;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class ContatoEnderecoController extends Controller
{   
    protected $respose;

    public function __construct(ContatoEndereco $ContatoEndereco)
    {
        $this->ContatoEndereco = $ContatoEndereco;
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
        if( ! $this->ContatoEndereco->isValid($input = $request->all())){


            return response()->json($this->ContatoEndereco->errors, 400);

            //return Redirect::back()->withInput()->withErrors($this->contato->errors);

        }else{

            $this->ContatoEndereco->Cadastro_ID             = $request->get('Cadastro_ID');
            $this->ContatoEndereco->Tipo_Endereco_ID        = 26;
            $this->ContatoEndereco->CEP                     = $request->get('CEP');
            
            $this->ContatoEndereco->Logradouro              = $request->get('Logradouro');
            $this->ContatoEndereco->Numero                  = $request->get('Numero');
            $this->ContatoEndereco->Complemento             = $request->get('Complemento');
            $this->ContatoEndereco->Bairro                  = $request->get('Bairro');
            $this->ContatoEndereco->Cidade                  = $request->get('Cidade');
            $this->ContatoEndereco->UF                      = $request->get('UF');
            $this->ContatoEndereco->Referencia              = '';
            $this->ContatoEndereco->Situacao_ID             = '';
            $this->ContatoEndereco->Usuario_Cadastro_ID     = 0;
            $this->ContatoEndereco->Data_Cadastro           = date('Y-m-d h:i:s');

            $this->ContatoEndereco->save();

            return response()->json($this->ContatoEndereco, 201);
        }
    }

    /**
     * Exibe os endereços de determinado cliente indicado.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $indicacoes     = DB::table('cadastros_enderecos')
                                ->select(
                                    'cadastros_enderecos.Cadastro_Endereco_ID',
                                    'cadastros_enderecos.Cadastro_ID',
                                    'cadastros_enderecos.Tipo_Endereco_ID',
                                    'cadastros_enderecos.CEP',
                                    'cadastros_enderecos.Logradouro',
                                    'cadastros_enderecos.Numero',
                                    'cadastros_enderecos.Complemento',
                                    'cadastros_enderecos.Bairro',
                                    'cadastros_enderecos.Cidade',
                                    'cadastros_enderecos.UF',
                                    'cadastros_enderecos.Referencia'
                                )
                                ->where([
                                    ['cadastros_enderecos.Cadastro_ID','=', $id],
                                    ['cadastros_enderecos.Situacao_ID','=', '1']
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
        $rules  = array(
            'Logradouro'=> 'required|min:2',
            'Bairro'=> 'required|min:2',
            'Cidade'=>'required',
            'UF'=>'required',
            'CEP'=>'required|min:8|max:9',
        );

        $validator = Validator::make($request->all(), $rules);

        $this->ContatoEndereco = ContatoEndereco::find($id);

        if( $validator->fails()){

            //return Redirect::back()->withInput()->withErrors($this->contato->errors);
            return response()->json($this->ContatoEndereco->errors, 400);

        }else{

            $this->ContatoEndereco->CEP                     = $request->get('CEP');
            $this->ContatoEndereco->Logradouro              = $request->get('Logradouro');
            $this->ContatoEndereco->Numero                  = $request->get('Numero');
            $this->ContatoEndereco->Complemento             = $request->get('Complemento');
            $this->ContatoEndereco->Bairro                  = $request->get('Bairro');
            $this->ContatoEndereco->Cidade                  = $request->get('Cidade');
            $this->ContatoEndereco->UF                      = $request->get('UF');

            $this->ContatoEndereco->save();

            return response()->json($this->ContatoEndereco, 201);

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
        $endereco   = ContatoEndereco::find($id);

        $endereco->delete();

        return response()->json(201);
    }
}
