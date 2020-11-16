<?php

namespace Tests\Builders;

use App\Models\Lesson;
use Illuminate\Support\Arr;
use Tests\TestCase;

class SegmentBuilderTest extends TestCase {

    public function testBuilderAddsLesson() {
        $lesson = factory(Lesson::class)->create();
        $segment = SegmentBuilder::instance()->inLesson($lesson->id)->build();
        $this->assertDatabaseHas('segments', ['id' => $segment->id, 'lesson_id' => $lesson->id]);
    }

    public function testBuilderAddsDescriptionAndPointsToTask() {
        $taskData = [
            'points'      => 3,
            'description' => 'Odd numbers?',
        ];

        $segment = SegmentBuilder::instance()->withCMCTask($taskData)->build();

        $this->assertDatabaseHas('segments', ['id' => $segment->id]);
        $this->assertEquals(1, $segment->tasks()->count());
        $this->assertDatabaseHas('tasks', array_merge($taskData, ['id' => $segment->tasks()->get()[0]->id]));
    }

    public function testBuilderCMCOptionsAreRandomAmountWhenNotDefined() {
        $segment = SegmentBuilder::instance()->withCMCTask()->build();

        $this->assertDatabaseHas('segments', ['id' => $segment->id]);
        $this->assertEquals(1, $segment->tasks()->count());
        $this->assertTrue($segment->tasks()->get()[0]->cmc()->count() > 0);
    }

    public function testBuilderCMCOptionsAsInteger() {
        $segment = SegmentBuilder::instance()->withCMCTask([
            'options' => 6,
        ])->build();

        $this->assertDatabaseHas('segments', ['id' => $segment->id]);
        $this->assertEquals(1, $segment->tasks()->count());
        $this->assertEquals(6, $segment->tasks()->get()[0]->cmc()->count());
    }

    public function testBuilderCMCOptionsAsIntegerCreatesMultiple() {
        $segment = SegmentBuilder::instance()
                                 ->withCMCTask(['options' => 6])
                                 ->withCMCTask(['options' => 3])
                                 ->build();

        $this->assertDatabaseHas('segments', ['id' => $segment->id]);
        $this->assertEquals(2, $segment->tasks()->count());
        $this->assertEquals(6, $segment->tasks()->get()[0]->cmc()->count());
        $this->assertEquals(3, $segment->tasks()->get()[1]->cmc()->count());
    }

    public function testBuilderCMCOptionsAsCompactAssociativeArray() {
        $taskData = [
            'points'      => 3,
            'description' => 'Odd numbers?',
            'options'     => [
                'A'   => false,
                '3'   => true,
                '17'  => true,
                '2'   => false,
                '170' => false,
            ],
        ];

        $segment = SegmentBuilder::instance()->withCMCTask($taskData)->build();

        $this->assertDatabaseHas('segments', ['id' => $segment->id]);
        $this->assertEquals(1, $segment->tasks()->count());
        $task = $segment->tasks()->get()[0];
        $this->assertDatabaseHas('tasks', array_merge(Arr::only($taskData,['points','description']),['id' => $task->id]));
        $this->assertEquals(5, $task->cmc()->count());
        foreach($taskData['options'] as $option => $correct){
            $this->assertDatabaseHas('answers_cmc', [
                'task_id' => $task->id,
                'description' => $option,
                'correct' => $correct,
            ]);
        }
    }

    public function testBuilderCMCOptionsAsFullAssociativeArray() {
        $taskData = [
            'options' => [
                ['description' => 'abc', 'correct' => false],
                ['description' => '123', 'correct' => true],
                ['description' => 'ABC', 'correct' => false],
            ],
        ];

        $segment = SegmentBuilder::instance()->withCMCTask($taskData)->build();

        $this->assertDatabaseHas('segments', ['id' => $segment->id]);
        $this->assertEquals(1, $segment->tasks()->count());
        $task = $segment->tasks()->get()[0];
        $this->assertDatabaseHas('tasks',['id' => $task->id]);
        $this->assertEquals(3, $task->cmc()->count());
        foreach($taskData['options'] as $option){
            $this->assertDatabaseHas('answers_cmc', array_merge($option,['task_id' => $task->id]));
        }
    }
}
