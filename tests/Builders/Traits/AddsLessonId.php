<?php

namespace Tests\Builders\Traits;

trait AddsLessonId {

    /**
     * @param $lessonId
     *
     * @return $this
     */
    public function inLesson($lessonId) {
        $this->attributes['lesson_id'] = $lessonId;
        return $this;
    }
}