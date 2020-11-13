<?php

namespace App\Models\Segments;

class AnswerCmc extends HiddenAnswer {

    protected $table    = 'answers_cmc';
    public    $fillable = ['description', 'correct'];
    public    $hidden   = ['correct'];

    public function tasks() {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
