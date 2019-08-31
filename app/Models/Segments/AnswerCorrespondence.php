<?php

namespace App\Models\Segments;

use Illuminate\Database\Eloquent\Model;

class AnswerCorrespondence extends Model
{
  protected $table = 'answers_correspondence';
  public $fillable = ['side_a','side_b'];
  
  public function tasks()
  {
    return $this->belongsTo(Task::class,'task_id');
  }
}
