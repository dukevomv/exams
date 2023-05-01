<?php

namespace App\Models\Trial;

use App\Models\Lesson;
use App\Models\OTP;
use App\Models\Test;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;

class Trial extends Model {
    protected $fillable = ['email','details','status','uuid'];
    protected $casts = ['details' => 'array'];

    public static function boot() {
        parent::boot();
        static::creating(function($item) {
            $item->uuid = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 12);
            ;
        });
    }

    public function getProfessorUser() {
        return $this->users()->where('role',UserRole::PROFESSOR)->first();
    }
    public function getStudentUser() {
        return $this->users()->where('role',UserRole::STUDENT)->first();
    }

    public function users() {
        return $this->morphedByMany(User::class,'trialable','trial_entities');
    }

    public function lessons() {
        return $this->morphedByMany(Lesson::class,'trialable','trial_entities');
    }

    public function tests() {
        return $this->morphedByMany(Test::class,'trialable','trial_entities');
    }

    public function sendPendingOTPAndGetUser(): User {
        $user = $this->getProfessorUser();
        OTP::generateForMail($user->mailable_email,true,$user);
        return $user;
    }



    /**
     * @param $timestamp
     * @param $role
     *
     * @return string
     */
    public static function generateEmailForRole($postfix, $role) {
        return $role . '+' . $postfix . '@' . config('app.trial.email_suffix');
    }

    public static function generateNameFromEmail($email) {
        $emailParts = explode('@', $email);
        return ucfirst($emailParts[0]);
    }
}
