<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Mail\OrcamentosFechadosMailable;

Route::get('/', function () {
    //return view('welcome');
    return Redirect::to('https://parcerias.eficazsystem.com.br');
});

Route::get('/enviar', function () {

	$data = array(
                'nomeCliente'            => '0',
                'nomeFantasiaCliente'    => '0',
                'idOrcamento'            => '0',
                'tituloOrcamento'        => '0',
                'valorTotalOrcamento'    => '0',
                'statusOrcamento'        => '0',
                'tipoPagamento'          => '0',
                'dataVencimento'         => '0',
                'dataVencimentoParceiro' => '0',
                'dadosParceiro'          => '0'
                            
                );

    Mail::to('sistemaeficaz@sistema.eficazsystem.com.br')->send(new OrcamentosFechadosMailable($data));

    return "done";
});
