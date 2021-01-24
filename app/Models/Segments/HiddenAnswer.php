<?php

namespace App\Models\Segments;

use App\Enums\UserRole;
use Auth;
use Illuminate\Database\Eloquent\Model;

class HiddenAnswer extends Model {

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $user = Auth::user();
        if (is_null($user) || $user->role != UserRole::STUDENT) {
            //$this->makeVisible($this->hidden);
        }
    }
}
