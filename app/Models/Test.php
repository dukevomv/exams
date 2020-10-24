<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Lesson;
use App\Models\Segments\Segment;
use Auth;
use Carbon\Carbon;

use Kreait\Firebase\Firebase;
use Kreait\Firebase\Configuration;

use App\Traits\Searchable;
class Test extends Model
{
    
  use Searchable;
  private $search = ['name'];
  protected $appends = ['user_on_test','can_register','register_time'];
  public $fillable = ['lesson_id','name','description','scheduled_at','duration','status'];
  protected $dates = ['scheduled_at','started_at','finished_at','graded_at'];
  
  public function lesson() {
    return $this->belongsTo(Lesson::class);
  }

  public function segments() {
    return $this->belongsToMany(Segment::class)->orderBy('position','asc')->withTimestamps();
  }

  public function users() {
    return $this->belongsToMany(User::class)->withTimestamps()->withPivot('status','answers','grade');
  }
  
  public function user() {
    return $this->belongsToMany(User::class)->where('user_id',Auth::id())->withTimestamps()->withPivot('status','answers','grade');
  }

  public function started_by() {
    return $this->belongsTo(User::class,'started_by_user');
  }
  
  public function finished_by() {
    return $this->belongsTo(User::class,'finished_by_user');
  }
  
  public function getUserOnTestAttribute() {
		return $this->users()->where('user_id',Auth::id())->first();
  }
  
  public function getRegisterTimeAttribute() {
		return is_null($this->scheduled_at) ? $this->scheduled_at : Carbon::parse($this->scheduled_at)->subMinutes(30);
  }
  
  public function getCanRegisterAttribute() {
		return Carbon::now()->gte($this->register_time);
  }
  
  public function register() {
      $firebase = app('firebase');
    $student = Auth::user();
    $firebase->update([
      'name'=>$student->name,
      'registered_at'=>Carbon::now()->toDateTimeString()
    ],'tests/'.$this->id.'/students/'.$student->id);
    
		$this->users()->attach(Auth::id(), ['status' =>'registered']);
  }
  
  public function leave() {
      $firebase = app('firebase');
    
    $student = Auth::user();
    
    $firebase->delete('tests/'.$this->id.'/students/'.$student->id);
    
		$this->users()->updateExistingPivot($student->id,['status'=>'left']);
  }

    public function getStudentsAnswers($userID, $final = false) {
        //'test_user.answers'
        return $this->users()->where('user_id',$userID)->select();
    }

    public function saveStudentsAnswers($userID,array $answers, $final = false) {
        return $this->users()->updateExistingPivot($userID,$this->getAnswersFields($answers,$final));
    }

    private function getAnswersFields(array $answers, $final){
      $field_data = 'answers';
      $field_date = 'answered';

        if(!$final){
            $field_data .= '_draft';
            $field_date .= '_draft';
        }
        $field_date .= '_at';

        return [
          $field_data => json_encode($answers),
          $field_date => Carbon::now()
        ];
    }
  
  public function start() {
		$this->status = 'started';
		$this->started_at = Carbon::now()->addSeconds(30);
		$this->started_by_user = Auth::id();

      $firebase = app('firebase');
    $student = Auth::user();
    
    $firebase->update([
      'started_at'=>Carbon::now()->toDateTimeString()
    ],'tests/'.$this->id);
    
		$this->save();
  }

  public function finish() {
		$this->status = 'finished';
		$this->finished_at = Carbon::now()->addSeconds(30);
		$this->finished_by_user = Auth::id();

      $firebase = app('firebase');
    $student = Auth::user();
    
    $firebase->update([
      'finished_at'=>Carbon::now()->toDateTimeString()
    ],'tests/'.$this->id);
    
		$this->save();
  }
}
