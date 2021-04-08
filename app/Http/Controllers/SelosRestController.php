<?php

namespace App\Http\Controllers;

use App\LojaPromotor;
use App\RetornoSelo;
use App\Selo;
use App\SeloValidado;
use App\Usuario;
use App\Utils\ConnectionUtil;
use App\Utils\Constants;
use App\Utils\SeloUtil;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SelosRestController extends Controller
{

    public function selos(Request $request)
    {
        Log::channel('daily')->debug('SelosRestController#selos - INICIO');

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

        $this->logOperacaoInicial($input, $cpf, $promotor, $vendedor, $validador, $qtdselos, $lojaEnvio);

        $strConnection = ConnectionUtil::getConnection($cpf);

        $lojas = DB::connection($strConnection)->table('loja_promotor')
            ->select('loja')
            ->where('cpf', $cpf)
            ->get();

        $LOJA_OK = $this->getSitucaoLoja($lojas, $lojaEnvio);

        $retornoSelo = new RetornoSelo();
        foreach ($selos as $s) {
            $seloRetornado = DB::connection($strConnection)->table('selos')
                ->where('qrcode', $s['qrcode'])
                ->first();

            if ($seloRetornado != null) {
                $seloValidado = new SeloValidado();

                Log::channel('daily')->debug(sprintf('SelosRestController#selos - SELO %s ENCONTRADDO! :) ', $s['qrcode']));
                Log::channel('daily')->debug(sprintf('SelosRestController#selos - SELO %s - Status %s ', $seloRetornado->qrcode, $seloRetornado->status));
                if (Constants::STATUS_UTILIZADO == $seloRetornado->status) {
                    Log::channel('daily')->debug(sprintf('SelosRestController#selos - O SELO %s JA FOI UTILIZADO!', $seloRetornado->qrcode));
                    $seloValidado->selo = $seloRetornado;
                    $seloValidado->mensagem = "SELO JA FOI UTILIZADO";
                    $operacao = "TENTATIVA DE LEITURA DE SELO JA UTILIZADO";
                    $mensagem = "SELO DE NUMERO (" . $seloRetornado->numero . ") JA FOI UTILIZADO";
                    Log::channel('operacao')->error(sprintf('%s;%s;%s', $operacao, $cpf, $mensagem));
                    $retornoSelo->addValue($seloValidado);
                } elseif (Constants::STATUS_VALIDADO == $seloRetornado->status) {
                    Log::channel('daily')->debug(sprintf('SelosRestController#selos - O SELO %s JA FOI VALIDADO!', $seloRetornado->qrcode));
                    $seloValidado->selo = $seloRetornado;
                    $seloValidado->mensagem = "SELO JA FOI VALIDADO";
                    $operacao = "TENTATIVA DE LEITURA DE SELO JA VALIDADO";
                    $mensagem = "SELO DE NUMERO (" . $seloRetornado->numero . ") JA FOI VALIDADO";
                    Log::channel('operacao')->error(sprintf('%s;%s;%s', $operacao, $cpf, $mensagem));
                    $retornoSelo->addValue($seloValidado);
                } elseif (Constants::STATUS_CRIADO == $seloRetornado->status) {
                    Log::channel('daily')->debug(sprintf('SelosRestController#selos - O SELO %s COM STATUS CORRETO!', $seloRetornado->qrcode));
                    if ($LOJA_OK) {
                        if (!$this->lojaIsValid($lojas, $seloRetornado->loja)) {
                            Log::channel('daily')->debug(sprintf('SelosRestController#selos - O SELO %s TEM LOJA INVÁLIDA!', $seloRetornado->qrcode));
                            $seloValidado->selo = $seloRetornado;
                            $seloValidado->mensagem = "LOJA INVALIDA";
                            $operacao = "TENTATIVA DE LEITURA DE SELO COM LOJA INVALIDA";
                            $mensagem = "SELO DE NUMERO (" . $seloRetornado->numero . ") LOJA INVALIDA";
                            Log::channel('operacao')->error(sprintf('%s;%s;%s', $operacao, $cpf, $mensagem));
                            $retornoSelo->addValue($seloValidado);
                        } else {
                            Log::channel('daily')->debug(sprintf('SelosRestController#selos - O SELO %s COM TUDO OK', $seloRetornado->qrcode));
                            SeloUtil::atualizaSelo($strConnection, $seloRetornado, $promotor, $vendedor, null, $lojaEnvio, Constants::STATUS_UTILIZADO);
                        }
                    } else {
                        Log::channel('daily')->debug(sprintf('SelosRestController#selos - O SELO %s TEM LOJA INVÁLIDA!', $seloRetornado->qrcode));
                        $seloValidado->selo = $seloRetornado;
                        $seloValidado->mensagem = "LOJA INVALIDA";
                        $operacao = "TENTATIVA DE LEITURA DE SELO COM LOJA INVALIDA";
                        $mensagem = "SELO DE NUMERO (" . $seloRetornado->numero . ") LOJA INVALIDA";
                        Log::channel('operacao')->error(sprintf('%s;%s;%s', $operacao, $cpf, $mensagem));
                        $retornoSelo->addValue($seloValidado);
                    }
                }
            } else {
                Log::channel('daily')->debug(sprintf('SelosRestController#selos - O SELO %s NAO FOI ENCONTRADO!', $s['qrcode']));
                $seloValidado = new SeloValidado();
                $seloValidado->selo = $s;
                $seloValidado->mensagem = "SELO INEXISTENTE";
                $operacao = "TENTATIVA DE LEITURA DE SELO INVALIDO E/OU INEXISTENTE!";
                $mensagem = "SELO COM QRCODE (" . $s['qrcode'] . ") NAO EXISTE NA BASE";
                Log::channel('operacao')->info(sprintf('%s;%s;%s', $operacao, $cpf, $mensagem));
                $retornoSelo->addValue($seloValidado);
            }
        }

        Log::channel('daily')->debug(sprintf('SelosRestController#selos - FINAL - RESPONSE:[ %s ]', json_encode($retornoSelo)));

        return response()->json($retornoSelo);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection  $lojas
     * @param  Selo  $selo
     * @return bool
     */
    public function lojaIsValid($lojas, $loja): bool
    {
        $encontrou = false;
        foreach ($lojas as $lj) {
            if ($lj->loja == $loja) {
                $encontrou = true;
                break;
            }
        }

        return $encontrou;
    }

    public function logOperacaoInicial($input, $cpf, $promotor, $vendedor, $validador, $qtdselos, $lojaEnvio)
    {
        $operacao = "LEITURA DE " . $qtdselos . " SELO(S) PELO PROMOTOR " . $promotor . ". LOJA [" . $lojaEnvio . "]";
        if (!empty($vendedor)) {
            $operacao = "LEITURA DE " . $qtdselos . " SELO(S) DO PROMOTOR " . $promotor . " PELO VENDEDOR " . $vendedor;
        }
        if (!empty($validador)) {
            $operacao = "VALIDACAO DE " . $qtdselos . " SELO(S) DO PROMOTOR " . $promotor . " PELO VALIDADOR " . $validador;
        }
        Log::channel('daily')->debug(sprintf('SelosRestController#logOperacaoInicial - %s ', $operacao));
        Log::channel('operacao')->info(sprintf('%s;%s;%s', $operacao, $cpf, json_encode($input)));
    }

    public function getSitucaoLoja($lojas, $lojaEnvio): bool
    {
        $LOJA_OK = true;
        Log::channel('daily')->debug(sprintf('SelosRestController#getSitucaoLoja - SITUACAO LOJA PROMOTOR [ %s ]', $LOJA_OK));

        if (isset($lojas) && !empty($lojas) && !empty($lojaEnvio)) {
            Log::channel('daily')->debug(sprintf('SelosRestController#getSitucaoLoja - Loja: [ %s ]. Lojas: %s', $lojaEnvio, json_encode($lojas)));
            // TEM QUE TER ENVIADO UMA LOJA E ELA TEM QUE SER DAS LOJAS DO PROMOTOR
            if (isset($lojaEnvio) && !empty($lojaEnvio)) {
                if (!$this->lojaIsValid($lojas, $lojaEnvio)) {
                    Log::channel('daily')->debug(sprintf('SelosRestController#getSitucaoLoja - NÃO ENCONTRAMOS A Loja: [ %s ]. Lojas: %s', $lojaEnvio, json_encode($lojas)));
                    $LOJA_OK = false;
                }
            }
        } else if (isset($lojas) && !empty($lojas) && empty($lojaEnvio)) {
            Log::channel('daily')->debug(sprintf('SelosRestController#getSitucaoLoja - Loja: [ vazia ]. Lojas: %s', json_encode($lojas)));
            $LOJA_OK = false;
        }
        Log::channel('daily')->debug(sprintf('SelosRestController#getSitucaoLoja - SITUACAO FINAL LOJA PROMOTOR [ %s ]', $LOJA_OK));

        return $LOJA_OK;
    }
}
