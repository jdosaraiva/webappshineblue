<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LojaPromotor extends Model
{
    protected $table = 'loja_promotor';

    protected $fillable = [
        'cpf',
        'loja',
    ];
}
