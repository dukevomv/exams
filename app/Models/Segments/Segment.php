<?php

namespace App\Models\Segments;

use App\Enums\TaskType;
use App\Models\Lesson;
use App\Models\Test;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model {

    use Searchable;

    private $search = ['title', 'description'];
    public  $fillable = ['lesson_id', 'title', 'description'];

    public function lesson() {
        return $this->BelongsTo(Lesson::class);
    }

    public function tests() {
        return $this->BelongsToMany(Test::class)->withTimestamps();
    }

    public function tasks() {
        return $this->hasMany(Task::class)->orderBy('position', 'asc');
    }

    public function scopeWithTaskAnswers($query) {
        return $query->with(['tasks' => function ($q) {
            $q->with([TaskType::RMC, TaskType::CMC, TaskType::CORRESPONDENCE, TaskType::FREE_TEXT,
            ])->orderBy('position');
        },
        ]);
    }
}
