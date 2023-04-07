<?php

namespace Tests\Feature\Segments;

use App\Enums\TaskType;
use App\Enums\UserRole;
use App\Models\Segments\Task;
use App\Models\User;
use Illuminate\Support\Arr;
use Tests\Builders\LessonBuilder;
use Tests\Builders\SegmentBuilder;
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

    public function testCanUpdateSegment() {
        $segment = SegmentBuilder::instance()
                           ->inLesson($this->lesson->id)
                           ->withRMCTask(['options'=>3])
                           ->withCMCTask(['options'=>3])
                           ->build();
        $updateData = [
          'id'=>$segment->id,
          'lesson_id'=>$this->lesson->id,
          'title'=>'Yoyoyoyo',
          'description'=>'abc',
        ];
        $response = $this->post('segments/update',$updateData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('segments',$updateData);
    }
    public function testCanUpdateSegmentTask() {
        $segment = SegmentBuilder::instance()
                           ->inLesson($this->lesson->id)
                           ->withCMCTask(['options'=>3])
                           ->withRMCTask(['options'=>3])
                           ->build();
        $updateData = [
          'id'=>$segment->id,
          'lesson_id'=>$this->lesson->id,
          'title'=>'Yoyoyoyo',
          'description'=>'abc',
          'tasks'=>[],
        ];

        $position = 1;
        foreach($segment->tasks as $t){
            $updateData['tasks'][] = [
              'id'  => $t->id,
              'type'  => $t->type,
              'description'  => 'A',
              'points'  => 2,
              'position'  => $position,
            ];
            $position++;
        }
        $response = $this->post('segments/update',$updateData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('segments',Arr::except($updateData,'tasks'));
        $this->assertDatabaseHas('tasks',array_merge($updateData['tasks'][0],['segment_id'=>$segment->id]));
    }
    public function testCanUploadSegmentTaskImages() {
        $segment = SegmentBuilder::instance()
          ->inLesson($this->lesson->id)
          ->withCMCTask(['options'=>3])
          ->withRMCTask(['options'=>3])
          ->build();
        $updateData = [
          'id'=>$segment->id,
          'lesson_id'=>$this->lesson->id,
          'title'=>'Yoyoyoyo',
          'description'=>'abc',
          'tasks'=>[],
        ];

        $position = 1;
        foreach($segment->tasks as $t){
            $updateData['tasks'][] = [
              'id'  => $t->id,
              'type'  => $t->type,
              'description'  => 'A',
              'points'  => 2,
              'position'  => $position,
            ];
            $position++;
        }
        $response = $this->post('tasks/images',$updateData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('segments',Arr::except($updateData,'tasks'));
        $this->assertDatabaseHas('tasks',$updateData['tasks'][0]);
    }

    public function testCanUpdateSegmentTaskImages() {}
    public function testCanDeleteSegmentTaskImages() {}
}
