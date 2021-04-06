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

Route::get('task','TaskController@index');
Route::get('task/{id}','TaskController@show');
Route::post('task','TaskController@store');
Route::put('task/{id}','TaskController@update');
Route::delete('task/{id}','TaskController@delete');

Route::group([
    'middleware'=>'api',
    'prefix' => 'auth'
], function($router){
    Route::post('login','AuthController@login');
    Route::post('register','AuthController@register');
    Route::post('logout', 'AuthController@logout');
});
