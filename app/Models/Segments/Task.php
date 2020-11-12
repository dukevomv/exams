<?php

namespace App\Models\Segments;

use App\Enums\TaskType;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {

    public $fillable = ['type', 'position', 'description', 'points'];

    public function rmc() {
        return $this->hasMany(AnswerRmc::class, 'task_id');
    }

    public function rmc_full() {
        return $this->hasMany(AnswerRmc::class, 'task_id')->select('answers_rmc.*');
    }

    public function cmc() {
        return $this->hasMany(AnswerCmc::class, 'task_id');
    }

    public function cmc_full() {
        return $this->hasMany(AnswerCmc::class, 'task_id')->select('answers_cmc.*');
    }

    public function free_text() {
        return $this->hasOne(AnswerFreeText::class, 'task_id');
    }

    public function free_text_full() {
        return $this->hasOne(AnswerFreeText::class, 'task_id')->select('answers_free_text.*');
    }

    public function correspondence() {
        return $this->hasMany(AnswerCorrespondence::class, 'task_id');
    }

    public function correspondence_full() {
        return $this->hasMany(AnswerCorrespondence::class, 'task_id')->select('answers_correspondence.*');
    }

    public function scopeAnswers($query) {
        return $query->with([TaskType::RMC,TaskType::CMC,TaskType::FREE_TEXT,TaskType::CORRESPONDENCE]);
    }
}
