<?php

namespace App\Utils;

use App\Usuario;
use Exception;
use Illuminate\Support\Facades\Log;

class ConnectionUtil
{
    public static function getConnection($cpf): string
    {
        $usuario = Usuario::where('cpf', $cpf)->first();
        if ($usuario == null) {
            Log::channel('operacao')->error(sprintf('%s;%s;%s', 'BUSCA DO CPF NA BASE', $cpf, 'CPF NAO ENCONTRADO NA BASE DE USUARIOS'));
            throw new Exception(sprintf('USUARIO [%s] NAO ENCONTRADO!', $cpf));
        }
        $conn = 'base_' . $usuario->base;

        Log::channel('daily')->debug(sprintf('ConnectionUtil#getConnection - USUARIO:[%s] - CONEXAO:[%s]', $cpf, $conn));
        return $conn;
    }
}