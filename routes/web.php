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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/simple', 'MainController@simples')->name('simple');
Route::post('/api/v2/login', 'LoginRestController@login')->name('login');
Route::post('/api/v2/senha', 'LoginRestController@alterarSenha')->name('alterarSenha');
Route::post('/api/v2/selos', 'SelosRestController@selos')->name('selos');
Route::post('/api/v2/lojas/{cpf}', 'LojasPromotorController@lojas')->name('lojas');
