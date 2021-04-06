<?php

namespace App\Http\Controllers;

use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MainController extends Controller
{
    public function simples()
    {
        Log::channel('daily')->debug('MainController#simples');

        $usuarios = Usuario::all();

        return response()->json($usuarios);

    }
}
