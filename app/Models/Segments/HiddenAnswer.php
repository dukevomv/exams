<?php

namespace App\Models\Segments;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Auth;

class HiddenAnswer extends Model {

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        if(Auth::user()->role != UserRole::STUDENT){
            $this->makeVisible($this->hidden);
        }
    }
}
