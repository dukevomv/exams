<?php

namespace App\Util;

use App\Enums\UserRole;

class UserIs {

    public static function approved($user) {
        return $user->approved == 1 && !self::withPendingOTP($user);
    }

    public static function withPendingOTP($user) {
        if(is_null($user)) {
            return true;
        }
        return $user->otp_enabled && $user->otp_pending;
    }

    public static function invitedDirectlyOnTest($user) {
        $user->loadMissing('invite');
        return !is_null($user->invite);
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

    public static function professorOrStudent($user) {
        return $user->role([UserRole::PROFESSOR, UserRole::STUDENT]);
    }

    public static function adminOrProfessor($user) {
        return $user->role([UserRole::ADMIN, UserRole::PROFESSOR]);
    }

    public static function notInTrial($user) {
        return is_null($user->trials()->first());
    }
}
