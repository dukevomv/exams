<?php

namespace App\Services;

use App\Models\Test;

interface TestServiceInterface {

    public function get(array $params);
    public function fetchById($id);
    public function setById($id);
    public function setTest(Test $test);

    public function calculateUserPoints($userId);
    public function autoGradeUser();
    public function gradeUserTask($payload);

    public function updatePublishedData();
    public function prepareForUser();
    public function prepareForPublish();

    public function toArray();
    public function toArraySegments();
    public function toArraySegment($s,$fromPublished);
}