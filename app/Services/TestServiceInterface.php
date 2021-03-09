<?php

namespace App\Services;

use App\Models\Test;

interface TestServiceInterface {

    public function get(array $params);
    public function fetchById($id);
    public function setById($id);
    public function setTest(Test $test);

    public function calculateUserPoints($userId);
    public function autoGradeForUser($userId);
    public function gradeUserTask($payload);
    public function publishTestGrades();

    public function updatePublishedData();
    public function prepareForCurrentUser();
    public function prepareForPublish();

    public function toArray();
    public function toArrayUsers();
    public function toArraySegments();
    public function toArraySegment($s,$fromPublished);
}