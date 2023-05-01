<?php

namespace App\Models;

use App\Models\Trial\Trial;
use App\Traits\Searchable;
use App\Util\UserIs;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Demoable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable {
    use Notifiable, Searchable, Demoable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'role', 'password'
    ];
    protected $search   = ['name', 'email'];
    protected $casts   = [
        'otp_enabled' => 'boolean',
        'otp_pending' => 'boolean'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function lessons() {
        return $this->belongsToMany(Lesson::class)->withPivot('pending');
    }

    public function invite() {
        return $this->hasOne(TestInvite::class);
    }

    /**
     * checks if the user belongs to a particular group
     *
     * @param string|array $role
     *
     * @return bool
     */
    /* The below is used as a method inside the Gates to check if current user is within correct roles */
    public function role($role) {
        $role = (array)$role;
        return in_array($this->role, $role);
    }

    public function hasPendingOTP() {
        return $this->otp_enabled && $this->otp_pending;
    }

    public function getMailableEmailAttribute() {
        $this->loadMissing('invite');
        if(UserIs::invitedDirectlyOnTest($this)){
            return $this->invite->student_email;
        } elseif(!UserIs::notInTrial($this)){
            return $this->trials()->first()->email;
        } else {
            return $this->email;
        }
    }

    public function via($notifiable) {
        return ['mail', 'database'];
    }
}
