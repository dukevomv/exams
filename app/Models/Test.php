<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Lesson;
use App\Models\Segments\Segment;
use Auth;
use Carbon\Carbon;
class Test extends Model
{
  public $fillable = ['lesson_id','name','description','scheduled_at','duration'];
  public function lesson() {
    return $this->BelongsTo(Lesson::class);
  }

  public function segments() {
    return $this->belongsToMany(Segment::class)->orderBy('position','asc')->withTimestamps();
  }

  public function users() {
    return $this->belongsToMany(User::class)->withTimestamps()->withPivot('status','grade');
  }

  public function register() {
		$this->users()->attach(Auth::id(), ['status' =>'registered']);
  }

  public function start() {
		$this->status = 'started';
		$this->started_at = Carbon::now();
		$this->save();
  }

  public function finish() {
		$this->status = 'finished';
		$this->finished_at = Carbon::now();
		$this->save();
  }
}
