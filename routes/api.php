<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//LISTA DE USUARIOS
Route::get('/users', 'UserController@index');

//LOGIN APP 
Route::post('/login', 'UserController@authenticateapp');

//CADASTRA USUARIO APP
Route::post('/users_app', 'UserController@store_app');

// CATEGORIAS ESTABELECIMENTO
Route::get('/categorias_estabelecimento', 'CategoriasEstabelecimentoController@index');
Route::post('/categorias_estabelecimento', 'CategoriasEstabelecimentoController@store');
Route::put('/categorias_estabelecimento/{id}', 'CategoriasEstabelecimentoController@update');
Route::delete('/categorias_estabelecimento/{id}', 'CategoriasEstabelecimentoController@delete');

// ESTABELECIMENTO
Route::get('/estabelecimento', 'EstabelecimentosController@index');
Route::post('/estabelecimento', 'EstabelecimentosController@store');
Route::put('/estabelecimento/{id}', 'EstabelecimentosController@update');
Route::delete('/estabelecimento/{id}', 'EstabelecimentosController@delete');