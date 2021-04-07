<?php
namespace App;

class RetornoSelo {
    public $selosValidados = [];

    public function addValue($seloValidado) {
        array_push($this->selosValidados, $seloValidado);
    }

    public function get() {
        return $this->selosValidados;
    }

    public function __toString() {
        return json_encode($this);
    }

}
