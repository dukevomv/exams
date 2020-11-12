<?php

namespace App\Models\Segments;

use App\Models\Lesson;
use App\Models\Test;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model {

    use Searchable;

    private $search   = ['title', 'description'];
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

    public function scopeWithTaskAnswers($query, $correct = false) {
        return $query->with(['tasks' => function ($q) use ($correct) {
            if ($correct) {
                $withs = ['rmc_full', 'cmc_full', 'correspondence_full'];
            } else {
                $withs = ['rmc', 'cmc', 'correspondence'];
            }
            $q->with($withs)->orderBy('position');
        },
        ]);
    }
}
