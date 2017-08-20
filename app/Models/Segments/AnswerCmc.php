<?php

namespace App\Models\Segments;

use Illuminate\Database\Eloquent\Model;

class AnswerCmc extends Model
{
  protected $table = 'answers_cmc';
  
  public function tasks()
  {
    return $this->belongsTo(Task::class,'task_id');
  }
}
