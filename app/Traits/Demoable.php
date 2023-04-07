<?php

namespace App\Traits;

use App\Models\Demo\DemoEntity;
use App\Models\Demo\DemoUser;
use App\Models\Trial\Trial;
use App\Models\Trial\TrialEntity;
use App\Scopes\OnlyDemoScope;
use App\Scopes\OnlyTrialScope;
use App\Scopes\WithoutDemoScope;
use App\Scopes\WithoutTrialScope;
use App\Util\Demo;
use App\Util\UserIs;
use Illuminate\Support\Facades\Auth;

trait Demoable {
    //this Trait applies for Trial entries as well

    public function addDemoableEntity(){
        $this->addEntityOfType('demo');
    }
    public function addTrialableEntity(){
        $this->addEntityOfType('trial');
    }

    private function addEntityOfType($type){
        $sessionValue = Demo::getSessionValueOfMode($type);
        if(config('app.'.$type.'.enabled') && !is_null($sessionValue)){
            $this->{$type.'_entity'}()->create([ Demo::IDENTIFIER_FIELDS[$type] => $sessionValue]);
        }
    }

    public function demo_entity() {
        return $this->morphOne(DemoEntity::class, 'demoable');
    }
    public function trial_entity() {
        return $this->morphOne(TrialEntity::class, 'trialable');
    }

    public function trials() {
        return $this->morphToMany(Trial::class,'trialable','trial_entities');
    }

    public function demos() {
        //duke|todo|debt - test this actually works
        return $this->morphToMany(DemoUser::class,'demoable','demo_entities');
    }

    public function getTrial() {
        return $this->trials()->first();
    }

    public function getDemo() {
        return $this->demos()->first();
    }

    protected static function boot(){
        static::created(function ($model) {
            $model->addDemoableEntity();
            $model->addTrialableEntity();
        });

        $mode = Demo::getModeFromSessionIfAny();

        if(!Auth::guest() && !UserIs::withPendingOTP(Auth::user())){
            if(!is_null($mode)){
                static::addGlobalScope($mode == Demo::DEMO ? new OnlyDemoScope : new OnlyTrialScope);
            } else {
                static::addGlobalScope(new WithoutDemoScope);
                static::addGlobalScope(new WithoutTrialScope);
            }
        }
        parent::boot();
    }

    private static function getScopesByMode($mode){
        switch ($mode){
            case Demo::DEMO:
                return ['only'=>OnlyDemoScope::class,'without'=>WithoutDemoScope::class];
            case Demo::TRIAL:
                return ['only'=>OnlyTrialScope::class,'without'=>WithoutTrialScope::class];
        }
    }

}
