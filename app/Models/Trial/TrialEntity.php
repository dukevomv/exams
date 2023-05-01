<?php

namespace App\Models\Trial;

use Illuminate\Database\Eloquent\Model;

class TrialEntity extends Model {
    public $timestamps = false;
    protected $fillable = ['trial_id'];

    public function trialable(){
        return $this->morphTo();
    }
}
