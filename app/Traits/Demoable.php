<?php

namespace App\Traits;

use App\Models\Demo\DemoEntity;
use App\Models\Trial\TrialEntity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

trait Demoable {

    //this Trait applies for Trial entries as well

    public static $identifierFields = [
        'demo' => 'demo_user_id',
        'trial' => 'trial_id'
    ];

    public function addDemoableEntity(){
        $this->addEntityOfType('demo');
    }
    public function addTrialableEntity(){
        $this->addEntityOfType('trial');
    }

    private function addEntityOfType($type){
        if(config('app.'.$type.'.enabled') && Session::has(config('app.'.$type.'.session_field'))){
            $this->{$type.'_entity'}()->create([ self::$identifierFields[$type] => Session::get(config('app.'.$type.'.session_field'))]);
        }
    }

    public function demo_entity() {
        return $this->morphOne(DemoEntity::class, 'demoable');
    }
    public function trial_entity() {
        return $this->morphOne(TrialEntity::class, 'trialable');
    }

    protected static function boot(){
        static::created(function ($model) {
            $model->addDemoableEntity();
            $model->addTrialableEntity();
        });

        $mode = null;
        foreach (['demo','trial'] as $type) {
            if (config('app.'.$type.'.enabled')
                && Session::has(config('app.' . $type . '.session_field'))) {
                    $mode = $type;
                    break;
            }
        }
        if(!is_null($mode)){
            static::addGlobalScope('only'.ucfirst($mode), function (Builder $builder) use ($mode){
                $builder->whereHas($mode.'_entity', function($q) use ($mode) {
                    $q->where(self::$identifierFields[$mode], Session::get(config('app.'.$mode.'.session_field')));
                });
            });
        } else {
            static::addGlobalScope('without'.ucfirst($type), function (Builder $builder) use ($type) {
                $builder->has($type.'_entity', '=', 0);
            });
        }

        parent::boot();
    }

}
