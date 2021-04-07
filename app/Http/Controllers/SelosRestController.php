<?php

namespace App\Http\Controllers;

use App\LojaPromotor;
use App\Selo;
use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SelosRestController extends Controller
{

    public function selos(Request $request)
    {
        Log::channel('daily')->debug('SelosRestController#selos - Inicio');

        $input = $request->all();
        Log::channel('daily')->debug(sprintf('SelosRestController#selos - INPUT:[ %s ]', json_encode($input)));

        $promotor = $input['promotor'];
        $selos = $input['selos'];
        $vendedor = isset($input['vendedor']) ? $input['vendedor'] : null;
        $validador = isset($input['validador']) ? $input['validador'] : null;
        $lojaEnvio = isset($input['loja']) ? $input['loja'] : null;
        $cpf = $promotor;
        if (!empty($vendedor)) {
            $cpf = $vendedor;
        }
        $qtdselos = count($selos);
        Log::channel('daily')->debug(sprintf('SelosRestController#selos - Promotor:[%s]. Vendedor:[%s]. Validador:[%s]. Loja:[%s]. Selos:[%s]', $promotor, $vendedor, $validador, $lojaEnvio, json_encode($selos)));

        $operacao = "LEITURA DE " . $qtdselos . " SELO(S) PELO PROMOTOR " . $promotor . ". LOJA [" . $lojaEnvio . "]";
        if (!empty($vendedor)) {
            $operacao = "LEITURA DE " . $qtdselos . " SELO(S) DO PROMOTOR " . $promotor . " PELO VENDEDOR " . $vendedor;
        }
        if (!empty($validador)) {
            $operacao = "VALIDACAO DE " . $qtdselos . " SELO(S) DO PROMOTOR " . $promotor . " PELO VALIDADOR " . $validador;
        }
        Log::channel('daily')->debug(sprintf('SelosRestController#selos - %s ', $operacao));
        Log::channel('operacao')->info(sprintf('%s;%s;%s', $operacao, $cpf, json_encode($input)));

        $LOJA_OK = true;
        Log::channel('daily')->debug(sprintf('SelosRestController#selos - SITUACAO LOJA PROMOTOR [ %s ]', $LOJA_OK));

        $usuario = Usuario::where('cpf', $cpf)->first();
        if ($usuario == null) {
            return response(['mensagem' => 'USUARIO NAO ENCONTRADO'], 404);
        }
        $conn = 'base_' . $usuario->base;
        $lojas = DB::connection($conn)->table('loja_promotor')
            ->select('loja')
            ->where('cpf', $cpf)
            ->get();

        if (isset($lojas) && !empty($lojas) && !empty($lojaEnvio)) {
            Log::channel('daily')->debug(sprintf('SelosRestController#selos - Loja: [ %s ]. Lojas: %s', $lojaEnvio, json_encode($lojas)));
            // TEM QUE TER ENVIADO UMA LOJA E ELA TEM QUE SER DAS LOJAS DO PROMOTOR
            if (isset($lojaEnvio) && !empty($lojaEnvio)) {
                $encontrou = false;
                foreach ($lojas as $lj) {
                    if ($lojaEnvio == $lj->loja) {
                        $encontrou = true;
                        break;
                    }
                }
                if (!$encontrou) {
                    Log::channel('daily')->debug(sprintf('SelosRestController#selos - NÃO ENCONTRAMOS A Loja: [ %s ]. Lojas: %s', $lojaEnvio, json_encode($lojas)));
                    $LOJA_OK = false;
                }
            }
        } else if (isset($lojas) && !empty($lojas) && empty($lojaEnvio)) {
            Log::channel('daily')->debug(sprintf('SelosRestController#selos - Loja: [ vazia ]. Lojas: %s', json_encode($lojas)));
            $LOJA_OK = false;
        }
        Log::channel('daily')->debug(sprintf('SelosRestController#selos - SITUACAO FINAL LOJA PROMOTOR [ %s ]', $LOJA_OK));

        $selos = DB::connection('base_s')->table('selos')->limit(10)->get();

        return response()->json($selos);
    }
}
