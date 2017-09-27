<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


// get list of Contato
Route::get('contatos','ContatoController@index');
// get specific Contato
Route::get('contato/{id}','ContatoController@show');
// delete a Contato
Route::delete('contato/{id}','ContatoController@destroy');
// update existing Contato
Route::put('contato','ContatoController@store');
// create new Contato
Route::post('contato','ContatoController@store');

// Criar novo contato
Route::post('criarContato','ContatoController@store');
//Route::resource('contatos', 'ContatoController');
// Editar Contato existente
Route::put('editarContato/{id}','ContatoController@update');

// Cadastrar novo telefone para contato
Route::post('criarTelefoneContato','ContatoTelefoneController@store');
// Editar Telefone de Contato
Route::put('editarTelefoneContato/{id}','ContatoTelefoneController@update');
// Remover Telefone de Contato
Route::delete('removerTelefoneContato/{id}','ContatoTelefoneController@destroy');

// Cadastrar novo endereco para contato
Route::post('criarEnderecoContato','ContatoEnderecoController@store');
// Editar endereco de contato
Route::put('editarEnderecoContato/{id}','ContatoEnderecoController@update');
// Remover endereco de Contato
Route::delete('removerEnderecoContato/{id}','ContatoEnderecoController@destroy');


// Cadastro de novo orçamento
Route::post('criarNovoOrcamentoCliente','OrcamentoController@store');
// Recupera status do orçamento
Route::get('statusOrcamentoCliente/{id}','OrcamentoController@show');
