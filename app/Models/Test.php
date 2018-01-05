<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Lesson;
use App\Models\Segments\Segment;

class Test extends Model
{
  public $fillable = ['lesson_id','name','description','scheduled_at','duration'];
  public function lesson() {
    return $this->BelongsTo(Lesson::class);
  }

  public function segments() {
    return $this->belongsToMany(Segment::class)->orderBy('position','asc')->withTimestamps();
  }
}
