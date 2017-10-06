<?php

namespace App\Models\Segments;

use Illuminate\Database\Eloquent\Model;

use App\Models\Lesson;
use App\Models\Segments\Task;

class Segment extends Model
{
  public $fillable = ['lesson_id','title','description'];
  public function lesson()
  {
    return $this->BelongsTo(Lesson::class);
  }
  public function tasks()
  {
    return $this->hasMany(Task::class);
  }

  public function scopeWithTasksAnswers($query)
	{
		return $query->with(['tasks' => function($q) {
			$q->with(['rmc','cmc'])->orderBy('row_num');
		}]);
	}
}
