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
}
