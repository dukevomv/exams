<?php

namespace App\Models\Segments;

use Illuminate\Database\Eloquent\Model;

class AnswerRmc extends Model
{
  protected $table = 'answers_rmc';
  
  public function tasks()
  {
    return $this->belongsTo(Task::class,'task_id');
  }
}
