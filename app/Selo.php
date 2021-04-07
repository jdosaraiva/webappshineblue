<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Selo extends Model
{
    protected $table = 'selos';

    protected $fillable = [
        'numero',
        'qrcode',
        'status',
        'conferido',
        'loja',
        'promotor_cpf',
        'promotor_datetime',
        'usuario_cpf',
        'usuario_datetime',
        'validador_cpf',
        'validador_datetime',
        'pagamento',
        'loja_envio',
    ];

    public function toArray() : array
    {

        return [
            'id' => $this->id,
            'numero' => $this->numero,
            'qrcode' => $this->qrcode,
            'status' => $this->status,
            'conferido' => $this->conferido,
            'loja' => $this->loja,
            'promotor_cpf' => $this->promotor_cpf,
            'promotor_datetime' => $this->promotor_datetime,
            'usuario_cpf' => $this->usuario_cpf,
            'usuario_datetime' => $this->usuario_datetime,
            'validador_cpf' => $this->validador_cpf,
            'validador_datetime' => $this->validador_datetime,
            'pagamento' => $this->pagamento,
            'loja_envio' => $this->loja_envio,
        ];

    }

}
