<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificacaoController extends Controller
{
    public function notificacao()
    {
        return view('notificacao');
    }
}
