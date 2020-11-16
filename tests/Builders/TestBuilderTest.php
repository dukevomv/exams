<?php

namespace Tests\Builders;

use App\Enums\TaskType;
use App\Models\Lesson;
use Tests\TestCase;

class TestBuilderTest extends TestCase {

    public function testBuilderAddsLesson() {
        $lesson = factory(Lesson::class)->create();
        $test = TestBuilder::instance()->inLesson($lesson->id)->build();
        $this->assertDatabaseHas('tests', ['id' => $test->id, 'lesson_id' => $lesson->id]);
    }

    public function testBuilderCreatesSegmentWithTasksAndOptions() {
        $test = TestBuilder::instance()->withSegmentTasks([
            TaskType::CMC => [
                'points'      => 3,
                'description' => 'Odd numbers?',
                'options'     => 4,
            ],
            TaskType::RMC => [
                'points'      => 13,
                'description' => 'Choose numbers',
                'options'     => 5,
            ],
        ])->build();
        $this->assertDatabaseHas('tests', ['id' => $test->id]);
        $this->assertEquals(1, $test->segments()->count());
        $segs = $test->segments()->get();
        $this->assertEquals(2, $segs[0]->tasks()->count());

        $this->assertEquals(TaskType::CMC, $segs[0]->tasks()->get()[0]->type);
        $this->assertEquals(4, $segs[0]->tasks()->get()[0]->cmc()->count());
        $this->assertEquals('Odd numbers?', $segs[0]->tasks()->get()[0]->description);
        $this->assertEquals(3, $segs[0]->tasks()->get()[0]->points);

        $this->assertEquals(TaskType::RMC, $segs[0]->tasks()->get()[1]->type);
        $this->assertEquals(5, $segs[0]->tasks()->get()[1]->rmc()->count());
        $this->assertEquals('Choose numbers', $segs[0]->tasks()->get()[1]->description);
        $this->assertEquals(13, $segs[0]->tasks()->get()[1]->points);
    }

    public function testBuilderCreateMultipleSegments() {
        $test = TestBuilder::instance()->withSegmentTasks([
            TaskType::CMC => [
                'points'      => 3,
                'description' => 'Odd numbers?',
                'options'     => 4,
            ],
            TaskType::RMC => [
                'points'      => 13,
                'description' => 'Choose numbers',
                'options'     => 5,
            ],
        ])->withSegmentTasks([
            TaskType::CMC => [
                'points'      => 4,
                'description' => 'Second Odd numbers?',
                'options'     => 5,
            ],
            TaskType::RMC => [
                'points'      => 14,
                'description' => 'Second Choose numbers',
                'options'     => 6,
            ],
        ])->build();
        $this->assertDatabaseHas('tests', ['id' => $test->id]);
        $this->assertEquals(2, $test->segments()->count());

        $segs = $test->segments()->get();
        $this->assertEquals(2, $segs[0]->tasks()->count());

        $this->assertEquals(TaskType::CMC, $segs[0]->tasks()->get()[0]->type);
        $this->assertEquals(4, $segs[0]->tasks()->get()[0]->cmc()->count());
        $this->assertEquals('Odd numbers?', $segs[0]->tasks()->get()[0]->description);
        $this->assertEquals(3, $segs[0]->tasks()->get()[0]->points);

        $this->assertEquals(TaskType::RMC, $segs[0]->tasks()->get()[1]->type);
        $this->assertEquals(5, $segs[0]->tasks()->get()[1]->rmc()->count());
        $this->assertEquals('Choose numbers', $segs[0]->tasks()->get()[1]->description);
        $this->assertEquals(13, $segs[0]->tasks()->get()[1]->points);

        $this->assertEquals(2, $segs[1]->tasks()->count());

        $this->assertEquals(TaskType::CMC, $segs[1]->tasks()->get()[0]->type);
        $this->assertEquals(5, $segs[1]->tasks()->get()[0]->cmc()->count());
        $this->assertEquals('Second Odd numbers?', $segs[1]->tasks()->get()[0]->description);
        $this->assertEquals(4, $segs[1]->tasks()->get()[0]->points);

        $this->assertEquals(TaskType::RMC, $segs[1]->tasks()->get()[1]->type);
        $this->assertEquals(6, $segs[1]->tasks()->get()[1]->rmc()->count());
        $this->assertEquals('Second Choose numbers', $segs[1]->tasks()->get()[1]->description);
        $this->assertEquals(14, $segs[1]->tasks()->get()[1]->points);
    }
}
