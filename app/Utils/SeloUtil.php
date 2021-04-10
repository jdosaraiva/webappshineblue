<?php

namespace App\Utils;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SeloUtil
{

    public static function atualizaSelo($strConnection, $selo, $promotor, $vendedor, $validador, $lojaEnvio, $status)
    {

        Log::channel('daily')->debug(sprintf('SeloUtil#atualizaSelo - CONEXAO:[%s]. SELO:[%s]. PROMOTOR[%s]. VENDEDOR:[%s]. VALIDADOR:[%s]. LOJA:[%s]. STATUS:[%s]', $strConnection, $selo->qrcode, $promotor, $vendedor, $validador, $lojaEnvio, $status));

        $selo = DB::connection($strConnection)->table('selos')->find($selo->id);
        $agora = (new \DateTime())->format('Y-m-d H:i:s');
        $selo->promotor_cpf = $promotor;
        $selo->promotor_datetime = $agora;
        if (!empty($vendedor)) {
            $selo->usuario_cpf = $vendedor;
            $selo->usuario_datetime = $agora;
        }
        if (!empty($validador)) {
            $selo->validador_cpf = $validador;
            $selo->validador_datetime = $agora;
            $selo->conferido = Constants::CONFERIDO;
        }
        if (!empty($lojaEnvio)) {
            $selo->loja_envio = $lojaEnvio;
        }
        if (!empty($status)) {
            $selo->status = $status;
        } else {
            $selo->status = Constants::STATUS_UTILIZADO;
        }

        DB::connection($strConnection)->table('selos')
            ->where('id', $selo->id)
            ->update(self::toArray($selo));

        return DB::connection($strConnection)->table('selos')->where('id', $selo->id)->first();
    }

    public static function toArray($selo): array
    {
        return [
            'id' => $selo->id,
            'numero' => $selo->numero,
            'qrcode' => $selo->qrcode,
            'status' => $selo->status,
            'conferido' => $selo->conferido,
            'loja' => $selo->loja,
            'promotor_cpf' => $selo->promotor_cpf,
            'promotor_datetime' => $selo->promotor_datetime,
            'usuario_cpf' => $selo->usuario_cpf,
            'usuario_datetime' => $selo->usuario_datetime,
            'validador_cpf' => $selo->validador_cpf,
            'validador_datetime' => $selo->validador_datetime,
            'pagamento' => $selo->pagamento,
            'loja_envio' => $selo->loja_envio,
        ];
    }

}