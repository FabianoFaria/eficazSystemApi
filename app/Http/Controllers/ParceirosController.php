<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use EllipseSynergie\ApiResponse\Contracts\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Parceiro;


class ParceirosController extends Controller
{

    protected $respose;

    public function __construct(Parceiro $parceiro)
    {
        $this->parceiro = $parceiro;
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
         if( ! $this->parceiro->isValid($input = $request->all())){


            return response()->json($this->parceiro->errors, 400);

            //return Redirect::back()->withInput()->withErrors($this->contato->errors);

        }else{

            $this->parceiro->Nome_Parceiro = $request->get('Nome_Parceiro');

            $this->parceiro->save();

            return response()->json($this->parceiro, 201);

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
    public function update(Request $request, $id)
    {
        //
        $rules = array(
            'Nome_Parceiro'  => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if( $validator->fails()){

            //return Redirect::back()->withInput()->withErrors($this->contato->errors);
            return response()->json($this->parceiro->errors, 400);

        }else{

            $parceiro                   = Parceiro::find($id);

            $parceiro->Nome_Parceiro    = $request->get('Nome_Parceiro');

            $parceiro->save();

            return response()->json($parceiro, 201);
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
