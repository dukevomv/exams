<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Models\Demo\DemoableModel;
use App\Models\Trial\Trial;
use App\Scopes\OnlyTrialScope;
use App\Scopes\WithoutTrialScope;
use Emadadly\LaravelUuid\Uuids;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TestInvite extends DemoableModel {
    use Notifiable,Uuids;

    public $fillable = ['student_name','student_email'];
    public const DRAFT = 'draft';
    public const INVITED = 'invited';
    public const ACCEPTED = 'accepted';

    public function routeNotificationForMail(){
        return [$this->student_email => $this->student_name];
    }
    public function test() {
        return $this->belongsTo(Test::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function scopeNotifiedOnly($query) {
        return $query->has('notifications','>',0);
    }
    public function enableTrialSessionIfAny(): ?Trial {
        $trial = $this->getTrial();
        if(!is_null($trial)){
            Session::put(config('app.trial.session_guest_field'), $trial->id);
        }
        return $trial;
    }
    public function accept(): User {
        //todo|debt - add db transaction here

        $test = $this->test()->withoutGlobalScope(WithoutTrialScope::class)->withoutGlobalScope(OnlyTrialScope::class)->first();
        $lesson = $test->lesson()->withoutGlobalScope(WithoutTrialScope::class)->withoutGlobalScope(OnlyTrialScope::class)->first();
        $user = User::create([
            'email' => $this->unique_user_email,
            'name' => $this->student_name,
            'password' => 'disabled',
            'role' => UserRole::STUDENT
        ]);
        $user->approved = true;
        $user->otp_enabled = true;
        $user->save();
        $this->user_id = $user->id;
        $lesson->users()->withoutGlobalScope(WithoutTrialScope::class)->withoutGlobalScope(OnlyTrialScope::class)->attach($user->id,['approved'=>true]);
        $test->register($user);
        $this->save();
        return $user;
    }
    public function loginRequest(): User {
        $user = $this->user()->withoutGlobalScope(WithoutTrialScope::class)->withoutGlobalScope(OnlyTrialScope::class)->first();
        OTP::generateForMail($this->student_email,true,$user);
        return $this->user;
    }

    public function getInvitedStatusAttribute() {
        return $this->notifications()->count() > 0;
    }
    public function getStatusAttribute() {
        if($this->invited_status){
            return is_null($this->user_id) ? self::INVITED : self::ACCEPTED;
        } else {
            return self::DRAFT;
        }
    }

    public function getUniqueUserEmailAttribute(){
        [$prefix,$suffix] = explode('@',$this->student_email);
        return $prefix.'+'.$this->uuid.'@'.$suffix;
    }
}
