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
Route::get('/users/{id}', 'UserController@show');
Route::post('/users', 'UserController@store');
Route::post('/users/{id}', 'UserController@update');
Route::delete('/users/{id}', 'UserController@delete');

//LOGIN APP 
Route::post('/login', 'UserController@authenticateapp');

//CADASTRA USUARIO APP
Route::post('/users_app', 'UserController@store_app');

//USERS SORTEIO APP
Route::post('/users_sorteio', 'UsersSorteioController@store');

//USERS DEVICE APP
Route::post('/users_device', 'UsersDeviceController@store');



// CATEGORIAS ESTABELECIMENTO
Route::get('/categorias_estabelecimento', 'CategoriasEstabelecimentoController@index');
Route::get('/categorias_estabelecimento/{id}', 'CategoriasEstabelecimentoController@show');
Route::post('/categorias_estabelecimento', 'CategoriasEstabelecimentoController@store');
Route::post('/categorias_estabelecimento/{id}', 'CategoriasEstabelecimentoController@update');
Route::delete('/categorias_estabelecimento/{id}', 'CategoriasEstabelecimentoController@delete');

// ESTABELECIMENTO
Route::get('/estabelecimento', 'EstabelecimentosController@index');
Route::get('/estabelecimento/{id}', 'EstabelecimentosController@show');
Route::post('/estabelecimento', 'EstabelecimentosController@store');
Route::post('/estabelecimento/{id}', 'EstabelecimentosController@update');
Route::delete('/estabelecimento/{id}', 'EstabelecimentosController@delete');


// ATRACOES ESTABELECIMENTO
Route::get('/atracoes_estabelecimento', 'AtracoesEstabelecimentosController@index');
Route::get('/atracoes_estabelecimento/{id}', 'AtracoesEstabelecimentosController@show');
Route::post('/atracoes_estabelecimento', 'AtracoesEstabelecimentosController@store');
Route::post('/atracoes_estabelecimento/{id}', 'AtracoesEstabelecimentosController@update');
Route::delete('/atracoes_estabelecimento/{id}', 'AtracoesEstabelecimentosController@delete');

// SORTEIOS
Route::get('/sorteios', 'SorteiosController@index');
Route::get('/sorteios/{id}', 'SorteiosController@show');
Route::post('/sorteios', 'SorteiosController@store');
Route::post('/sorteios/{id}', 'SorteiosController@update');
Route::delete('/sorteios/{id}', 'SorteiosController@delete');

//ATRACOES
Route::get('/atracoes_app', 'AtracoesEstabelecimentosController@showApp');

//SORTEIO ATIVO
Route::get('/sorteio_ativo', 'SorteiosController@showApp');

// DASHBOARD
Route::get('/dashboard', 'EstabelecimentosController@dashboard');