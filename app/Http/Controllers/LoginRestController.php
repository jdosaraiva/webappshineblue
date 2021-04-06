<?php

namespace App\Http\Controllers;

use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginRestController extends Controller
{
    public function login(Request $request)
    {

        Log::channel('daily')->debug('LoginRestController#login');

        $input = $request->all();

        Log::channel('daily')->debug(sprintf('LoginRestController#login - [ %s ]', json_encode($input)));
        Log::channel('daily')->debug(sprintf('LoginRestController#login - CPF:[ %s ]', $input["cpf"]));

        $tempUsuario = Usuario::where('cpf', $input['cpf'])
            ->where('senha', $input['senha'])
            ->first();

        if ($tempUsuario != null) {
            return response()->json($tempUsuario);
        } 

        Log::channel('daily')->info("LoginRestController - NAO ENCONTROU ");
        $rawData = ['error' => 'CPF|CNPJ ou Senha Invalido!'];		

        return response(json_encode($rawData), 404);
    }
}
