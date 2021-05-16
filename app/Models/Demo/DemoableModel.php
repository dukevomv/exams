<?php

namespace App\Models\Demo;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Demoable;

class DemoableModel extends Model {
    use Demoable;
}
