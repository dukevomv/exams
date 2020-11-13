<?php

namespace App\Models\Segments;

use App\Enums\TaskType;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {

    public $fillable = ['type', 'position', 'description', 'points'];

    public function rmc() {
        return $this->hasMany(AnswerRmc::class, 'task_id');
    }

    public function cmc() {
        return $this->hasMany(AnswerCmc::class, 'task_id');
    }

    public function free_text() {
        return $this->hasOne(AnswerFreeText::class, 'task_id');
    }

    public function correspondence() {
        return $this->hasMany(AnswerCorrespondence::class, 'task_id');
    }

    public function scopeAnswers($query) {
        return $query->with([TaskType::RMC,TaskType::CMC,TaskType::FREE_TEXT,TaskType::CORRESPONDENCE]);
    }
}
