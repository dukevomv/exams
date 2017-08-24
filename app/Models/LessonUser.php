<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use Auth;
use DB;
class LessonUser extends Model
{
  public $table = 'lesson_user';
}
