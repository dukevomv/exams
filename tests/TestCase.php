<?php

namespace Tests;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, AuthenticatesUsers, DatabaseMigrations;

    protected function setUp() {
        parent::setUp();

        $this->app->extend('filesystem', function ($service) {
            return \Mockery::mock($service)
                           ->allows('temporaryUrl')->zeroOrMoreTimes()->andReturnUsing(function($path) {
                    return "test.location" . $path;
                })->getMock();
        });
    }
}
