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

Route::get('/', function () {
    //return view('welcome');
    return Redirect::to('https://parcerias.eficazsystem.com.br');
});

Route::get('/enviar', function () {
    $user = User::find(2);

    \Mail::to('sistemaeficaz@sistema.eficazsystem.com.br')->send(new OrcamentosFechadosMailable($data));

    return "done";
});
