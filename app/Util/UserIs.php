<?php

namespace App\Util;

use App\Enums\UserRole;

class UserIs {
    public static function approved($user) {
        return $user->approved == 1;
    }
    public static function admin($user) {
        return $user->role(UserRole::ADMIN);
    }
    public static function professor($user) {
        return $user->role(UserRole::PROFESSOR);
    }
    public static function student($user) {
        return $user->role(UserRole::STUDENT);
    }
    public static function adminOrProfessor($user) {
        return $user->role(UserRole::ADMIN,UserRole::PROFESSOR);
    }
    public static function professorOrStudent($user) {
        return $user->role(UserRole::PROFESSOR,UserRole::STUDENT);
    }
}
