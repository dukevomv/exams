<?php

namespace App\Models\Segments;

use Illuminate\Database\Eloquent\Model;

use App\Models\Segments\AnswerRmc;
use App\Models\Segments\AnswerCmc;

class Task extends Model
{
  public function rmc()
  {
    return $this->hasMany(AnswerRmc::class,'task_id');
  }
  public function cmc()
  {
    return $this->hasMany(AnswerCmc::class,'task_id');
  }

  public function scopeAnswers($query)
	{
    return $query->with(['rmc','cmc']);
	}
}
