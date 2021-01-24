<?php

namespace App\Traits;

trait Searchable {

    public function scopeSearch($query, $search = '') {
        if ($search == '' || !isset($this->search)) return $query;

        return $query->where(function ($query) use ($search) {
            $query->where($this->search[0], 'like', '%' . $search . '%');
            for ($i = 1; $i < count($this->search); $i++) {
                $query->orWhere($this->search[1], 'like', '%' . $search . '%');
            }
        });
    }
}
