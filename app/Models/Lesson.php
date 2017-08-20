<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Lesson extends Model
{
  public function professors()
  {
    return $this->belongsToMany(User::class)->where('role','professor');
  }
}
