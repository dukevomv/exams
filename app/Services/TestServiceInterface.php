<?php

namespace App\Services;

use App\Models\Test;

interface TestServiceInterface {

    public function get(array $params);

    public function calculateUserPoints(Test $test, $userId);

    public function prepareForUser(Test $test);
}