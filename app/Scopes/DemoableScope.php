<?php

namespace App\Scopes;

use App\Util\Demo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;

abstract class DemoableScope implements Scope
{

    public function onlyEntity($mode,Builder $builder){
        $sessionValue = Demo::getSessionValueOfMode($mode);
        if(!is_null($sessionValue)){
            $builder->whereHas($mode.'_entity', function($q) use ($mode,$sessionValue) {
                $q->where(Demo::IDENTIFIER_FIELDS[$mode], $sessionValue);
            });
        }
    }

    public function withoutEntity($mode,Builder $builder){
        $builder->has($mode.'_entity', '=', 0);
    }
}