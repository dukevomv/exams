<?php

namespace App\Exceptions;

class InvalidOperationException extends \RuntimeException {

    protected $data;

    public function __construct($message, $data) {
        $this->data = $data;
        parent::__construct($message);
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }
}