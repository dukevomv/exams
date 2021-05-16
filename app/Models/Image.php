<?php

namespace App\Models;

use App\Traits\HandlesUploads;
use Illuminate\Database\Eloquent\Model;

class Image extends Model {
    use HandlesUploads;
    protected $guarded = [];
    protected $appends = ['url'];

    public function imageable(){
        return $this->morphTo();
    }
}
