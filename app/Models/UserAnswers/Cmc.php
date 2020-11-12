<?php

namespace App\Models\UserAnswers;

use Illuminate\Database\Eloquent\Model;

class Cmc extends Model {

    protected $table    = 'test_user_answers_cmc';
    public    $fillable = ['user_id', 'correct'];
    public    $hidden   = ['correct'];

    public function tasks() {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
