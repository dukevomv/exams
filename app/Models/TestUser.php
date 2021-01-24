<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TestUser extends Pivot {

//todo add here all the cast methods dates etc
    protected $casts = [
        'answers'       => 'array',
        'answers_draft' => 'array',
    ];
}