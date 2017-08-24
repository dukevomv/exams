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

  public function scopeFilterUserId($query,$user_id = null) {
  	$user_id = is_null($user_id) ? Auth::user()->id : $user_id;
    return $query->users()->wherePivot('user_id',$user_id);
  }
}
