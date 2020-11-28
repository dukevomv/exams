<?php

namespace App\Services;

use App\Models\Test;

interface TestServiceInterface {

    public function get(array $params);

    public function calculateUserPoints(Test $test, $userId);

    public function prepareForUser(Test $test);

    public function toArray(Test $test);
    public function toArraySegment($segment);
    public function toArraySegments($segments);

    //todo include the TestService used functions and what i want to have published
}