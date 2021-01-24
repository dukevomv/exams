<?php

namespace App\Models;

use App\Traits\Searchable;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model {

    use Searchable;

    protected $search   = ['name', 'gunet_code'];
    public    $fillable = ['name', 'gunet_code', 'semester'];

    public function users() {
        return $this->belongsToMany(User::class)->withPivot('user_id', 'approved', 'created_at', 'updated_at');
    }

    public function status() {
        return $this->belongsTo(LessonUser::class, 'id', 'lesson_id')->where('user_id', Auth::user()->id);
    }


    public function pending_users() {
        return $this->belongsToMany(User::class)->wherePivot('approved', 0);
    }

    public function pending_students() {
        return $this->pending_users()->where('role', 'student');
    }

    public function pending_professors() {
        return $this->pending_users()->where('role', 'professor');
    }


    public function approved_users() {
        return $this->belongsToMany(User::class)->wherePivot('approved', 1);
    }

    public function approved_students() {
        return $this->approved_users()->where('role', 'student');
    }

    public function approved_professors() {
        return $this->approved_users()->where('role', 'professor');
    }

    public function scopePending($query) {
        return $query->whereHas('status', function ($query) {
            $query->where('approved', 0);
        });
    }

    public function scopeApproved($query) {
        return $query->whereHas('status', function ($query) {
            $query->where('approved', 1);
        });
    }

    public function scopeUnsubscribed($query) {
        return $query->has('status', '=', 0);
    }
}
