<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TestUser extends Pivot
{
    protected $casts = [
        'answers' => 'array',
        'answers_draft' => 'array'
    ];
}