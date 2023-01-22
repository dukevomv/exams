<?php

namespace App\Models\Trial;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Trial extends Model {
    protected $fillable = ['email','details','status','uuid'];
    protected $casts = ['details' => 'array'];

    public static function boot() {
        parent::boot();
        static::creating(function($item) {
            $item->uuid = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 12);
            ;
        });
    }

    public function getProfessorUser() {
        return $this->users()->where('role',UserRole::PROFESSOR)->first();
    }
    public function getStudentUser() {
        return $this->users()->where('role',UserRole::STUDENT)->first();
    }

    public function lesson() {
        return $this->belongsToMany(User::class);
    }

    public function users() {
        return $this->morphedByMany(User::class,'trialable','trial_entities');
    }



    /**
     * @param $timestamp
     * @param $role
     *
     * @return string
     */
    public static function generateEmailForRole($postfix, $role) {
        return $role . '+' . $postfix . '@' . config('app.trial.email_suffix');
    }

    public static function generateNameFromEmail($email) {
        $emailParts = explode('@', $email);
        return ucfirst($emailParts[0]);
    }
}
