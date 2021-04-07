<?php

namespace App;

class SeloValidado {
    public $selo;
    public $mensagem;

    public function __toString() {
        return json_encode($this);
    }

}
