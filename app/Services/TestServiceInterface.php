<?php

namespace App\Services;

use App\Models\Test;

interface TestServiceInterface {

    public function get(array $params);

    public function calculateUserPoints(Test $test, $userId);

    public function gradeUserTask(Test $test, $payload);

    public function autoGradeUser(Test $test);

    public function prepareForUser(Test $test);

    public function prepareForPublish(Test $test);

    public function updatePublishedData(Test $test);

    public function toArray(Test $test);

    public function toArraySegment($segment, $grades = []);

    public function toArraySegments(Test $test, $withGrades = true);

    //todo include the TestService used functions and what i want to have published
}