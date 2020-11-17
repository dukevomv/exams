<?php

namespace Tests\Builders;

use App\Enums\TestStatus;
use App\Enums\TestUserStatus;
use App\Models\Test;
use Carbon\Carbon;
use Tests\Builders\Traits\AddsLessonId;

/**
 * Class TestBuilder
 *
 * @package Tests\Builders
 */
class TestBuilder extends ModelBuilder {

    use AddsLessonId;

    private $segments = [];
    private $users    = [];

    /**
     * @param null $date
     *
     * @return $this
     */
    public function published($date = null) {
        $this->attributes['status'] = TestStatus::PUBLISHED;
        $this->attributes['scheduled_at'] = !is_null($date) ? $date : Carbon::now();
        return $this;
    }

    /**
     * @return $this
     */
    public function draft() {
        $this->attributes['status'] = TestStatus::DRAFT;
        return $this;
    }

    /**
     * @param null $date
     *
     * @return $this
     */
    public function started($date = null) {
        $this->attributes['status'] = TestStatus::STARTED;
        $this->attributes['scheduled_at'] = !is_null($date) ? $date : Carbon::now()->subMinute();
        $this->attributes['started_at'] = !is_null($date) ? $date : Carbon::now();
        return $this;
    }

    /**
     * @param null $date
     *
     * @return $this
     */
    public function finished($date = null) {
        $this->attributes['status'] = TestStatus::FINISHED;
        $this->attributes['scheduled_at'] = !is_null($date) ? $date : Carbon::now()->subMinutes(2);
        $this->attributes['started_at'] = !is_null($date) ? $date : Carbon::now()->subMinute();
        $this->attributes['finished_at'] = !is_null($date) ? $date : Carbon::now();
        return $this;
    }

    public function withSegmentTasks($tasks = []) {
        $this->segments[] = $tasks;
        return $this;
    }

    /**
     * Adds user with status on test.
     * @param $userId
     * @param string $status Defaults to 'registered'
     *
     * @return $this
     */
    public function withUser($userId, $status = TestUserStatus::REGISTERED) {
        $this->users[] = ['user_id' => $userId, 'status' => $status];
        return $this;
    }

    /**
     * @return Test
     */
    public function build() {
        $attrs = array_merge([], $this->attributes);

        $test = factory(Test::class)->create($attrs);
        $this->buildSegments($test);

        //todo  make below subscribed users
        //$person->departments()->attach(Arr::pluck($this->departments, 'id'));

        return $test;
    }

    private function buildSegments(Test $test) {
        $ordered_segments = [];
        $position = 1;
        foreach ($this->segments as $seg) {
            $builder = SegmentBuilder::instance()->inLesson($test->lesson_id);
            foreach ($seg as $task) {
                $builder->withTask($task['type'], $task);
            }
            $position++;
            $ordered_segments[$builder->build()->id] = ['position' => $position];
        }
        $test->segments()->sync($ordered_segments);
        return $test;
    }
}