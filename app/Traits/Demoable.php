<?php

namespace App\Traits;

use App\Models\Demo\DemoEntity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

trait Demoable {
    public function addDemoableEntity(){
        if(config('app.demo.enabled') && Session::has(config('app.demo.session_field'))){
            $this->demo_entity()->create(['demo_user_id'=>Session::get(config('app.demo.session_field'))]);
        }
    }

    public function demo_entity() {
        return $this->morphOne(DemoEntity::class, 'demoable');
    }

    protected static function boot(){
        static::created(function ($model) {
            $model->addDemoableEntity();
        });

        if(config('app.demo.enabled')) {
            if (Session::has(config('app.demo.session_field'))) {
                static::addGlobalScope('onlyDemo', function (Builder $builder) {
                    $builder->whereHas('demo_entity', function($q){
                        $q->where('demo_user_id', Session::get(config('app.demo.session_field')));
                    });
                });
            } else {
                static::addGlobalScope('withoutDemo', function (Builder $builder) {
                    $builder->has('demo_entity', '=', 0);
                });
            }
        }

        parent::boot();
    }

}
