<?php

namespace App\Models\Segments;

class AnswerRmc extends HiddenAnswer {

    protected $table    = 'answers_rmc';
    public    $fillable = ['description', 'correct'];
    public    $hidden   = ['correct'];

    public function tasks() {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
