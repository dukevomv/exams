<?php

namespace Tests\Builders;

use App\Models\Lesson;
use App\Models\Test;
use Illuminate\Support\Arr;

/**
 * Class TestBuilder
 *
 * @package Tests\Builders
 */
class LessonBuilder extends ModelBuilder {

    private $users = [];
    /**
     * Adds user on test (pivot).
     *
     * @param $userId
     * @param array $pivot Pivot data, Defaults to ['status'=>'registered']
     *
     * @return $this
     */
    public function withUser($userId, $approved = true) {
        $this->users[$userId] = ['approved' => $approved ? 1 : 0];
        return $this;
    }

    /**
     * @return Test
     */
    public function build() {
        $attrs = array_merge([], $this->attributes);

        $lesson = factory(Lesson::class)->create($attrs);

        foreach ($this->users as $userId => $pivot) {
            $lesson->users()->attach($userId, Arr::only($pivot, ['approved']));
        }

        return $lesson;
    }
}