<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{

    protected $table = 'usuario';

    protected $fillable = [
        'nome', 'cpf', 'loja', 'senha', 'nivel', 'status', 'base',
    ];

    public function getNivel() : string
    {
        switch($this->nivel) {
            case 1 :
                return 'Promotor';
            case 2 :
                return 'Vendedor';
            case 3 :
                return 'Validador';
            case 4 :
                return 'Master';
            default :
                return 'Nível não definido';
        } 
    }

}
