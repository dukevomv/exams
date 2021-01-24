<?php

namespace App\Services;

use App\Models\Test;

interface TestServiceInterface {

    public function get(array $params);

    public function calculateUserPoints(Test $test, $userId);

    public function gradeUserTask(Test $test, $payload);

    public function prepareForUser(Test $test);

    public function toArray(Test $test);

    public function toArraySegment($segment, $grades = []);

    public function toArraySegments(Test $test);

    //todo include the TestService used functions and what i want to have published
}