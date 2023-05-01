<?php

namespace App\Scopes;

use App\Util\Demo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OnlyDemoScope extends DemoableScope
{
    public function apply(Builder $builder, Model $model)
    {
        $this->onlyEntity(Demo::DEMO,$builder);
    }
}