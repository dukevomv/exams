<?php

namespace Tests\Feature\Tests;

use App\Enums\TaskType;
use App\Enums\UserRole;
use App\Models\Segments\Task;
use App\Models\User;
use Tests\Builders\LessonBuilder;
use Tests\Builders\TestBuilder;
use Tests\TestCase;

class ProfessorTest extends TestCase {

    private $user;
    private $lesson;

    protected function setUp() {
        parent::setUp();

        $this->user = factory(User::class)->states([UserRole::PROFESSOR])->create();
        $this->lesson = LessonBuilder::instance()->withUser($this->user->id)->build();
        $this->actingAs($this->user);
    }

    public function testCanLoadTestPreviewPage() {
        $test = TestBuilder::instance()
                           ->inLesson($this->lesson->id)
                           ->published()
                           ->withUser($this->user->id)
                           ->build();
        $response = $this->get('/tests/' . $test->id);
        $response->assertStatus(200);
    }
    public function testCanStartTest() {}
    public function testCanPreviewStartedTest() {
        $test = TestBuilder::instance()
                           ->inLesson($this->lesson->id)
                           ->withSegmentTasks([['type' => TaskType::CMC, 'options' => 3]])
                           ->withSegmentTasks([['type' => TaskType::RMC, 'options' => 3]])
                           ->withSegmentTasks([['type' => TaskType::FREE_TEXT]])
                           ->started()
                           ->withUser($this->user->id)
                           ->build();
        $response = $this->get('/tests/' . $test->id);
        $response->assertStatus(200);
    }
    public function testCanFinishTest() {}
    public function testCanPreviewFinishedTest() {

    }
    public function testCanPreviewGradedTest() {

    }
}
