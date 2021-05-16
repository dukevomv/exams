<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HandlesUploads;

class Image extends Model
{
    use HandlesUploads;
    protected $guarded = [];
    protected $appends = ['url'];

    public function imageable(){
        return $this->morphTo();
    }
}
