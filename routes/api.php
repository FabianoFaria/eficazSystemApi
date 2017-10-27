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
// Listar Contatos
Route::get('listarContatos/{id}','ContatoController@listarIndicacoesParceiros');
//Listar total de contatos indicados pelo parceiro
Route::get('totalContatos/{id}','ContatoController@totalIndicacoesParceiros');

//Listar Telefones cadastrados do cliente indicado
Route::get('listarTelefones/{id}','ContatoTelefoneController@show');
//Carregar Telefone especifico para atualização
Route::get('carregarTelefone/{id}','ContatoTelefoneController@edit');
// Cadastrar novo telefone para contato
Route::post('criarTelefoneContato','ContatoTelefoneController@store');
// Editar Telefone de Contato
Route::put('editarTelefoneContato/{id}','ContatoTelefoneController@update');
// Remover Telefone de Contato
Route::delete('removerTelefoneContato/{id}','ContatoTelefoneController@destroy');


//Listar Enderecos cadastrados do cliente indicado
Route::get('listarEnderecos/{id}','ContatoEnderecoController@show');
//Carrega Endereço especifico para atualização
Route::get('carregarEndereco/{id}','ContatoEnderecoController@edit');
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
// Recupera dados mais completos do orçamento
Route::get('orcamentoClienteDetalhado/{id}','OrcamentoController@orcamentoDetalhado');


// Cadastro de parceiros 
Route::post('criarParceiro','ParceirosController@store');
// Editar nome de parceiro
Route::put('editarParceiro/{id}','ParceirosController@update');



// Lista o total de orçamentos de um dos parceiros
Route::get('totalOrcamentosParceiro/{id}','OrcamentoController@totalOrcamentosParceiro');
// Listar os orçamentos e ou chamados de determinado cliente
Route::get('listarOrcamentoCliente/{id}','OrcamentoController@listarOrcamentosCliente');
// Listar os orçamentos e ou chamados relacioandos ao parceiro
Route::get('listarOrcamento/{id}','OrcamentoController@statusOrcamentosParceiro');