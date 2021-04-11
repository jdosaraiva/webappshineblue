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
    return view('login');
});

Route::post('/api/v2/login', 'LoginRestController@login')->name('login');
Route::post('/api/v2/senha', 'LoginRestController@alterarSenha')->name('alterarSenha');
Route::post('/api/v2/selos', 'SelosRestController@selos')->name('selos');
Route::post('/api/v2/lojas/{cpf}', 'LojasPromotorController@lojas')->name('lojas');
Route::post('/api/v2/validar', 'ValidacaoRestController@validar')->name('validar');
Route::post('/login', 'LoginController@login')->name('telaLogin');
Route::post('/logoff', 'LoginController@logoff')->name('logoff');
Route::get('/menu', 'LoginController@menu')->name('menu');
Route::get('/notificacao', 'NotificacaoController@notificacao')->name('notificacao');
