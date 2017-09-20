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


// // get list of Contato
// Route::get('contatos','ContatoController@index');
// // get specific Contato
// Route::get('contato/{id}','ContatoController@show');
// // delete a Contato
// Route::delete('contato/{id}','ContatoController@destroy');
// // update existing Contato
// Route::put('contato','ContatoController@store');
// // create new Contato
// Route::post('contato','ContatoController@store');

Route::resource('contatos', 'ContatoController');