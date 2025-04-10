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

Route::prefix('coop')->group(function() {
    Route::get('/', 'COOPController@index');
    Route::get('get_coops', 'COOPController@get_coops');
    Route::get('create', 'COOPController@create');
    Route::post('store', 'COOPController@store');
    Route::get('{id}/show', 'COOPController@show');
    Route::get('{id}/edit', 'COOPController@edit');
    Route::post('{id}/update', 'COOPController@update');
    Route::get('{id}/destroy', 'COOPController@destroy');
    Route::get('{id}/remove_user', 'COOPController@remove_user');
    Route::post('{id}/add_user', 'COOPController@add_user');
});
