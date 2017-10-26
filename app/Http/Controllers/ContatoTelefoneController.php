<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use EllipseSynergie\ApiResponse\Contracts\Response;
use App\ContatoTelefone;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class ContatoTelefoneController extends Controller
{
    protected $respose;


    public function __construct(ContatoTelefone $ContatoTelefone)
    {
        $this->ContatoTelefone = $ContatoTelefone;
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
        if( ! $this->ContatoTelefone->isValid($input = $request->all())){


            return response()->json($this->ContatoTelefone->errors, 400);

            //return Redirect::back()->withInput()->withErrors($this->contato->errors);

        }else{

            $this->ContatoTelefone->Cadastro_ID             = $request->get('Cadastro_ID');
            $this->ContatoTelefone->Telefone                = $request->get('Telefone');
            $this->ContatoTelefone->Observacao              = $request->get('Observacao');

            $this->ContatoTelefone->Tipo_Telefone_ID        = 27;
            $this->ContatoTelefone->Situacao_ID             = 1;
            $this->ContatoTelefone->Data_Cadastro           = date('Y-m-d h:i:s');
            $this->ContatoTelefone->Usuario_Cadastro_ID     = 0;

            $this->ContatoTelefone->save();

            return response()->json($this->ContatoTelefone, 201);

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
         $telefones    = DB::table('cadastros_telefones')
                                ->select(
                                    'cadastros_telefones.Cadastro_Telefone_ID',
                                    'cadastros_telefones.Cadastro_ID',
                                    'cadastros_telefones.Telefone',
                                    'cadastros_telefones.Observacao'
                                )
                                ->where([
                                    ['cadastros_telefones.Cadastro_ID','=', $id],
                                    ['cadastros_telefones.Situacao_ID','=', '1']
                                ])
                                ->get();


        if(empty($telefones)){  
            return response()->json(null, 200);
        }else{
            return response()->json($telefones, 200);
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
        $telefone   = DB::table('cadastros_telefones')
                            ->select(
                                'cadastros_telefones.Cadastro_Telefone_ID',
                                'cadastros_telefones.Cadastro_ID',
                                'cadastros_telefones.Telefone',
                                'cadastros_telefones.Observacao'
                            )
                            ->where([
                                ['cadastros_telefones.Cadastro_Telefone_ID','=', $id],
                                ['cadastros_telefones.Situacao_ID','=', '1']
                            ])
                            ->get();

        if(empty($telefone)){  
            return response()->json(null, 200);
        }else{
            return response()->json($telefone, 200);
        }
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
           'Telefone'=> 'required|min:8',
           'Observacao'=> 'max:100'
        );

        $validator = Validator::make($request->all(), $rules);

        if( $validator->fails()){

            //return Redirect::back()->withInput()->withErrors($this->contato->errors);
            return response()->json($this->contato->errors, 400);

        }else{

            $ContatoTelefone = ContatoTelefone::find($id);

            $ContatoTelefone->Telefone      = $request->get('Telefone');
            $ContatoTelefone->Observacao    = $request->get('Observacao');

            $ContatoTelefone->save();

            return response()->json($ContatoTelefone, 201);
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
        $telefone = ContatoTelefone::find($id);

        $telefone->delete();

        return response()->json(201);

    }
}
