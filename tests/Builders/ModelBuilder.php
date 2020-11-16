<?php

namespace Tests\Builders;

abstract class ModelBuilder {

    protected $attributes = [];
    protected $faker;

    public function __construct() {
        $this->faker = \Faker\Factory::create();
    }


    public static function instance() {
        return new static();
    }

    abstract function build();

    public function withAttributes(array $attrs) {
        $this->attributes = $attrs;
        return $this;
    }

    public function appendAttributes(array $attrs) {
        $this->attributes = array_merge($this->attributes, $attrs);
        return $this;
    }
}