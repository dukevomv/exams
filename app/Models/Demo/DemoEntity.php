<?php

namespace App\Models\Demo;

use Illuminate\Database\Eloquent\Model;

class DemoEntity extends Model {
    public $timestamps = false;
    protected $fillable = ['demo_user_id'];

    public function demoable(){
        return $this->morphTo();
    }
}
