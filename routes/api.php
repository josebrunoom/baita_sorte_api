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