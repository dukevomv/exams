<?php

namespace App\Models;

use App\Models\Trial\Trial;
use App\Traits\Searchable;
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
        'name', 'email', 'role', 'password',
    ];
    protected $search   = ['name', 'email'];

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

    public function trials() {
        return $this->morphToMany(Trial::class,'trialable','trial_entities');
    }
}
