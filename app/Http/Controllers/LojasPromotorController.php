<?php

namespace App\Http\Controllers;

use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LojasPromotorController extends Controller
{
    public function lojas(Request $request, $cpf)
    {
        Log::channel('daily')->debug(sprintf('LojasPromotorController#lojas - INICIO - CPF:[%s]', $cpf));

        $operacao = "BUSCANDO AS LOJAS PARA O PROMOTOR " . $cpf;

        $usuario = Usuario::where('cpf', $cpf)->first();
        if ($usuario == null) {
            return response(['mensagem' => 'USUARIO NAO ENCONTRADO'], 404);
        }
        $conn = 'base_' . $usuario->base;
        $lojas = DB::connection($conn)->table('loja_promotor')
            ->select('loja')
            ->where('cpf', $cpf)
            ->get();

        $lojasRetorno = array();
        foreach ($lojas as $loja) {
            $lojasRetorno[] = $loja->loja;
        }
        Log::channel('operacao')->info(sprintf('%s;%s;LOJAS(%s)', $operacao, $cpf, json_encode($lojasRetorno)));

        return response()->json($lojasRetorno);
    }
}
