<?php

namespace App\Models\Segments;

use Illuminate\Database\Eloquent\Model;

class AnswerFreeText extends Model
{
  protected $table = 'answers_free_text';
  public $fillable = ['description'];
  
  public function tasks()
  {
    return $this->belongsTo(Task::class,'task_id');
  }
}
