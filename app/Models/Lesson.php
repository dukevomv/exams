<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\LessonUser;
use Auth;
use DB;
class Lesson extends Model
{
  public function users()
  {
    return $this->belongsToMany(User::class)->withPivot('user_id','approved');
  }

  public function status()
  {
    return $this->belongsTo(LessonUser::class,'id','lesson_id')->where('user_id',Auth::user()->id);
  }

  public function scopePending($query) {
    return $query->whereHas('status',function($query){
      $query->where('approved',0);
    });
  }

  public function scopeApproved($query) {
    return $query->whereHas('status',function($query){
      $query->where('approved',1);
    });
  }

  public function scopeUnsubscribed($query) {
    return $query->has('status','=',0);
  }
}
