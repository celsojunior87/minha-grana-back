<?php

use Illuminate\Http\Request;
use \App\Models\User;

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

Route::middleware('auth:api')->get('/auth', function (Request $request) {
    return $request->user();
});

Route::post('/user/cadastrar', 'UserController@cadastrar');
Route::post('/user/recuperar-senha', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/user/recuperar-senha', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/user/resetar-senha', 'Auth\ResetPasswordController@reset');


Route::group(['middleware' => 'auth:api'], function () {

    /**
     * Auth / Me
     */
    Route::get('/me', 'AuthController@me');

    /**
     * User
     */

    Route::get('user/pre-requisite', 'UserController@preRequisite');
    Route::post('/user/{id}/avatar', 'UserController@avatar');
    Route::resource('user', 'UserController');

    /**
     * Fornecedores
     */
    Route::resource('fornecedor', 'FornecedoresController');

    /**
     * Permissions
     */
    Route::prefix('permission')->group(function () {
        Route::get('/', 'PermissionController@index');
        Route::get('/assign/pre-requisite', 'PermissionController@preRequisite');
        Route::get('/{id}', 'PermissionController@show');
        Route::post('/', 'PermissionController@store');
        Route::delete('/{id}', 'PermissionController@destroy');
        Route::post('/assign', 'PermissionController@assignPermission');
    });

    /**
     * Roles
     */
    Route::prefix('role')->group(function () {
        Route::get('/', 'RoleController@index');
        Route::get('/{id}', 'RoleController@show');
        Route::put('/{id}', 'RoleController@update');
        Route::post('/', 'RoleController@store');
        Route::delete('/{id}', 'RoleController@destroy');
    });

    Route::prefix('order')->group(function () {
        Route::get('/')->uses('OrderController@index');
        Route::get('/{id}')->uses('OrderController@show')->where('id', '[0-9]+');
        Route::get('{id}/details')->uses('OrderController@details')->where('id', '[0-9]+');
    });
    Route::prefix('categoria')->group(function () {
        Route::get('/')->uses('CategoriaController@index');
        Route::post('/')->uses('CategoriaController@store');
        Route::get('/{id}')->uses('CategoriaController@show');
        Route::put('/{id}')->uses('CategoriaController@update');
        Route::delete('/{id}')->uses('CategoriaController@destroy');
    });
    Route::prefix('produtos')->group(function () {
        Route::get('/')->uses('ProdutoController@index');
        Route::post('/')->uses('ProdutoController@store');
        Route::put('/{id}')->uses('ProdutoController@update');
        Route::get('/{id}')->uses('ProdutoController@show');
        Route::delete('/{id}')->uses('ProdutoController@destroy');
    });
    Route::prefix('tipo_produto')->group(function () {
        Route::get('/')->uses('TipoProdutoController@index');
        Route::post('/')->uses('TipoProdutoController@store');
        Route::put('/{id}')->uses('TipoProdutoController@update');
        Route::get('/{id}')->uses('TipoProdutoController@show');
        Route::delete('/{id}')->uses('TipoProdutoController@destroy');
    });
    Route::prefix('produto')->group(function () {
        Route::get('/')->uses('ProdutoController@index');
        Route::post('/')->uses('ProdutoController@store');
        Route::put('/{id}')->uses('ProdutoController@update');
        Route::get('/{id}')->uses('ProdutoController@show');
        Route::delete('/{id}')->uses('ProdutoController@destroy');
    });
});
