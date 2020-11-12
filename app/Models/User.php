<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {

    use Notifiable, Searchable;

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
    public function role($role) {
        $role = (array)$role;
        return in_array($this->role, $role);
    }
}
