<?php

namespace App\Models\Segments;

use App\Enums\TaskType;
use App\Models\Image;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {

    public $fillable = ['type', 'position', 'description', 'points'];

    public $storage_prefix = 'public/tasks';
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
        return $query->with([TaskType::RMC, TaskType::CMC, TaskType::FREE_TEXT, TaskType::CORRESPONDENCE]);
    }

    public function images() {
        return $this->morphMany(Image::class, 'imageable');
    }
}
