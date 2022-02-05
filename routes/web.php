<?php

use Illuminate\Support\Facades\Route;


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


Route::get('/', 'App\Http\Controllers\NetworkController@index');
Route::post('/add/wallet_address', 'App\Http\Controllers\WalletController@store');
Route::get('/show/assets/{wallet_address}', 'App\Http\Controllers\WalletController@show_assets');


Route::post('/add/contract_address', 'App\Http\Controllers\ContractController@store');