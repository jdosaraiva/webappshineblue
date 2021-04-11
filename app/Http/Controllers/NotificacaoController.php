<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificacaoController extends Controller
{
    public function notificacao()
    {
        Log::channel('daily')->debug('LoginController#notificacao');

        if ('' == session()->get('usuario')) {
            Log::channel('daily')->debug('LoginController#notificacao - Sem usuário, sendo redirecinado para o login!');
            return redirect('/')->with('error','É necessário fazer o login!');;
        } 

        return view('notificacao');
    }
}
