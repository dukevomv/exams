<?php

namespace App\Services;

use App\Models\Test;
use App\Models\User;

interface TestServiceInterface {

    public function get(array $params);
    public function calculateUserPoints(Test $test, $userId);
}