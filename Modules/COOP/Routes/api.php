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

Route::middleware('auth:api')->get('/coop', function (Request $request) {
    return $request->user();
});
Route::prefix('v1/coop')->group(function() {
    Route::get('/', 'Api\v1\COOPController@index');
    Route::get('get_custom_fields', 'Api\v1\COOPController@get_custom_fields');
    Route::get('create', 'Api\v1\COOPController@create');
    Route::post('store', 'Api\v1\COOPController@store');
    Route::get('{id}/show', 'Api\v1\COOPController@show');
    Route::get('{id}/edit', 'Api\v1\COOPController@edit');
    Route::post('{id}/update', 'Api\v1\COOPController@update');
    Route::get('{id}/destroy', 'Api\v1\COOPController@destroy');
    Route::get('{id}/remove_user', 'Api\v1\COOPController@remove_user');
    Route::post('{id}/add_user', 'Api\v1\COOPController@add_user');
});