<?php

namespace App\Http\Controllers;

use App\RetornoSelo;
use App\SeloValidado;
use App\Utils\ConnectionUtil;
use App\Utils\Constants;
use App\Utils\SeloUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ValidacaoRestController extends Controller
{
    public function validar(Request $request)
    {
        Log::channel('daily')->debug('ValidacaoRestController#validar - INICIO');

        $input = $request->all();
        Log::channel('daily')->debug(sprintf('ValidacaoRestController#validar - INPUT:[ %s ]', json_encode($input)));

        $promotor = $input['promotor'];
        $vendedor = isset($input['vendedor']) ? $input['vendedor'] : null;
        $validador = isset($input['validador']) ? $input['validador'] : null;
        $selos = $input['selos'];
        $cpf = $promotor;
        $qtdselos = count($selos);
        $status = Constants::STATUS_VALIDADO;
        Log::channel('daily')->debug(sprintf('ValidacaoRestController#validar - Promotor:[%s]. Vendedor:[%s]. Validador:[%s]. Selos:[%s]', $promotor, $vendedor, $validador, json_encode($selos)));

        $this->logOperacaoInicial($input, $cpf, $promotor, $vendedor, $validador, $qtdselos, $lojaEnvio, $status);

        $strConnection = ConnectionUtil::getConnection($cpf);

        $retornoSelo = new RetornoSelo();
        foreach($selos as $s) {
            $seloRetornado = DB::connection($strConnection)->table('selos')
                ->where('qrcode', $s['qrcode'])
                ->first();

            if ($seloRetornado != null) {
                $seloValidado = new SeloValidado();

                if (Constants::STATUS_CRIADO == $seloRetornado->status) {
                    error_log("SelosRestController - O SELO " . $seloRetornado->qrcode . " AINDA NAO FOI ENVIADO!");
                    $seloValidado->selo = $seloRetornado;
                    $seloValidado->mensagem = "SELO AINDA NAO FOI ENVIADO";
                    $operacao = "TENTATIVA DE VALIDACAO DE SELO NAO ENVIADO";
                    $mensagem = "SELO DE NUMERO (" . $seloRetornado->numero . ") AINDA NAO FOI ENVIADO";
                    Log::channel('operacao')->error(sprintf('%s;%s;%s', $operacao, $cpf, $mensagem));
                    $retornoSelo->addValue($seloValidado);

                } else if (Constants::STATUS_UTILIZADO == $seloRetornado->status) {
                    $cpfPromotor = $seloRetornado->promotor_cpf;
                    if (!empty($vendedor) || !empty($validador)) {
                        if (!empty($cpfPromotor) && $cpf != $cpfPromotor) {
                            Log::channel('daily')->debug(sprintf('ValidacaoRestController#validar - SELO %s DIFERE DO PROMOTOR ENVIADO! ORIGEM %s - ATUAL %s', $seloRetornado->qrcode, $cpfPromotor, $cpf));
                            $seloValidado->selo = $seloRetornado;
                            $seloValidado->mensagem = "PROMOTOR " . $cpf . " DIFERENTE DO INFORMADO ANTES " . $cpfPromotor;
                            $operacao = "TENTATIVA DE VALIDACAO DE SELO COM PROMOTOR DIFERENTE";
                            $mensagem = "SELO DE NUMERO (" . $seloRetornado->numero . ") PROMOTOR " . $cpf . " DIFERENTE DO INFORMADO ANTES " . $cpfPromotor;
                            Log::channel('operacao')->error(sprintf('%s;%s;%s', $operacao, $cpf, $mensagem));
                            $retornoSelo->addValue($seloValidado);
                        } else {
                            if (!empty($vendedor)) {
                                SeloUtil::atualizaSelo($strConnection, $seloRetornado, $cpf, $vendedor, $validador, null, $status);
                            } else {
                                SeloUtil::atualizaSelo($strConnection, $seloRetornado, $cpf, $validador, $validador, null, $status);
                            }
                        }
                    } else {
                        Log::channel('daily')->debug(sprintf('ValidacaoRestController#validar - O SELO %s JA FOI VALIDADO!', $seloRetornado->qrcode));
                        $seloValidado->selo = $seloRetornado;
                        $seloValidado->mensagem = "SELO JA FOI VALIDADO";
                        $operacao = "TENTATIVA DE VALIDACAO DE SELO JA VALIDADO";
                        $mensagem = "SELO DE NUMERO (" . $seloRetornado->numero . ") JA FOI VALIDADO";
                        Log::channel('operacao')->error(sprintf('%s;%s;%s', $operacao, $cpf, $mensagem));
                        $retornoSelo->addValue($seloValidado);
                    }

                } else if (Constants::STATUS_VALIDADO == $seloRetornado->status) {
                    $cpfPromotor = $seloRetornado->promotor_cpf;
                    if (!empty($validador)) {
                        if (!empty($cpfPromotor) && $cpf != $cpfPromotor) {
                            Log::channel('daily')->debug(sprintf('ValidacaoRestController#validar - SELO %s DIFERE DO PROMOTOR ENVIADO! ORIGEM %s. ATUAL %s', $seloRetornado->qrcode, $cpfPromotor, $cpf));
                            $seloValidado->selo = $seloRetornado;
                            $seloValidado->mensagem = "PROMOTOR " . $cpf . " DIFERENTE DO INFORMADO ANTES " . $cpfPromotor;
                            $operacao = "TENTATIVA DE VALIDACAO DE SELO COM PROMOTOR DIFERENTE";
                            $mensagem = "SELO DE NUMERO (" . $seloRetornado->numero . ") PROMOTOR " . $cpf . " DIFERENTE DO INFORMADO ANTES " . $cpfPromotor;
                            Log::channel('operacao')->error(sprintf('%s;%s;%s', $operacao, $cpf, $mensagem));
                            $retornoSelo->addValue($seloValidado);
                        } else {
                            SeloUtil::atualizaSelo($strConnection, $seloRetornado, $cpf, $vendedor, $validador, null, $status);
                        }
                    } else {
                        Log::channel('daily')->debug(sprintf('ValidacaoRestController#validar - O SELO %s JA FOI VALIDADO!', $seloRetornado->qrcode));
                        $seloValidado->selo = $seloRetornado;
                        $seloValidado->mensagem = "SELO JA FOI VALIDADO";
                        $operacao = "TENTATIVA DE VALIDACAO DE SELO JA VALIDADO";
                        $mensagem = "SELO DE NUMERO (" . $seloRetornado->numero . ") JA FOI VALIDADO";
                        Log::channel('operacao')->error(sprintf('%s;%s;%s', $operacao, $cpf, $mensagem));
                        $retornoSelo->addValue($seloValidado);
                    }

                } else if (Constants::STATUS_CONFERIDO == $seloRetornado->status) {
                    Log::channel('daily')->debug(sprintf('ValidacaoRestController#validar - O SELO %s JA FOI VALIDADO INTERNAMENTE!', $seloRetornado->qrcode));
                    $seloValidado->selo = $seloRetornado;
                    $seloValidado->mensagem = "SELO JA FOI VALIDADO INTERNAMENTE";
                    $operacao = "TENTATIVA DE VALIDACAO DE SELO JA VALIDADO INTERNAMENTE";
                    $mensagem = "SELO DE NUMERO (" . $seloRetornado->numero . ") JA FOI VALIDADO INTERNAMENTE";
                    Log::channel('operacao')->error(sprintf('%s;%s;%s', $operacao, $cpf, $mensagem));
                    $retornoSelo->addValue($seloValidado);

                } else {
                    Log::channel('daily')->debug(sprintf('ValidacaoRestController#validar - O SELO %s TEM STATUS INVALIDO!', $seloRetornado->qrcode));
                    $seloValidado->selo = $seloRetornado;
                    $seloValidado->mensagem = "SELO COM STATUS INVALIDO";
                    $operacao = "TENTATIVA DE VALIDACAO DE SELO COM STATUS INVALIDO";
                    $mensagem = "SELO DE NUMERO (" . $seloRetornado->numero . ") COM STATUS INVALIDO";
                    Log::channel('operacao')->error(sprintf('%s;%s;%s', $operacao, $cpf, $mensagem));
                    $retornoSelo->addValue($seloValidado);
                }

            } else {
                Log::channel('daily')->debug(sprintf('ValidacaoRestController#validar - O SELO %s NAO FOI ENCONTRADO!', $s['qrcode']));
                $seloValidado = new SeloValidado();
                $seloValidado->selo = $s;
                $seloValidado->mensagem = "SELO INEXISTENTE";
                $operacao = "TENTATIVA DE VALIDACAO DE SELO INVALIDO E/OU INEXISTENTE!";
                $mensagem = "SELO COM QRCODE (" .  $s['qrcode'] . ") NAO EXISTE NA BASE";
                Log::channel('operacao')->error(sprintf('%s;%s;%s', $operacao, $cpf, $mensagem));
                $retornoSelo->addValue($seloValidado);
            }
                
        }

        Log::channel('daily')->debug(sprintf('ValidacaoRestController#validar - FINAL - RESPONSE:[ %s ]', json_encode($retornoSelo)));
        return response()->json($retornoSelo);
    }

    public function logOperacaoInicial($input, $cpf, $promotor, $vendedor, $validador, $qtdselos, &$status)
    {
        $operacao = "VALIDACAO DE " . $qtdselos . " SELO(S) PELO PROMOTOR " . $promotor;
        if (!empty($vendedor)) {
            $operacao = "VALIDACAO DE " . $qtdselos . " SELO(S) DO PROMOTOR " . $promotor . " PELO VENDEDOR " . $vendedor;
        } else if (!empty($validador)) {
            $operacao = "VALIDACAO DE " . $qtdselos . " SELO(S) DO PROMOTOR " . $promotor . " PELO VALIDADOR " . $validador;
            $status = Constants::STATUS_CONFERIDO;
        }

        Log::channel('daily')->debug(sprintf('ValidacaoRestController#logOperacaoInicial - %s ', $operacao));
        Log::channel('operacao')->info(sprintf('%s;%s;%s', $operacao, $cpf, json_encode($input)));
    }

}
