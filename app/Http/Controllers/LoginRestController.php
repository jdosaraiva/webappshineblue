<?php

namespace App\Http\Controllers;

use App\Usuario;
use Exception;
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

    public function alterarSenha(Request $request)
    {
        Log::channel('daily')->debug('LoginRestController#alterarSenha');

        $input = $request->all();
        $cpf = $input["cpf"];
        $operacao = "ALTERACAO DE SENHA - USUARIO " . $cpf;
        Log::channel('operacao')->info(sprintf('%s;%s;%s', $operacao, $cpf, json_encode($input)));
        $id = $input['id'];
        $senha = $input['senha'];

        try {
            $usuario = Usuario::findOrFail($id);
            if ($usuario != null) {
                $usuario->senha = $senha;
                $usuario->update($usuario->toArray());
                $mensagem = ['mensagem' => 'OK'];
                Log::channel('operacao')->info(sprintf('%s;%s;%s', $operacao, $cpf, json_encode($mensagem)));
                return response()->json($mensagem);
            } else {
                $mensagem = ['mensagem' => 'USUARIO NÃO ENCONTRADO'];
                Log::channel('operacao')->info(sprintf('%s;%s;%s', $operacao, $cpf, json_encode($mensagem)));
                return response(json_encode($mensagem), 404);
            }
        } catch (Exception $e) {
            $mensagem = $e->getMessage();
            Log::channel('operacao')->error(sprintf('%s;%s;%s', $operacao, $cpf, json_encode($mensagem)));
        }

        return response(json_encode($mensagem), 501);
    }
}
