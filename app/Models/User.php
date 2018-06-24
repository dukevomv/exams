<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\Lesson;

use App\Traits\Searchable;

class User extends Authenticatable
{
  use Notifiable, Searchable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'email', 'role', 'password',
  ];
  protected $search = ['name','email'];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token',
  ];

  public function lessons()
  {
    return $this->belongsToMany(Lesson::class)->withPivot('pending');
  }
}
