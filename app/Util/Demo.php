<?php

namespace App\Util;

class Demo {

    /**
     * @param $timestamp
     * @param $role
     *
     * @return string
     */
    public static function generateEmailForRole($timestamp, $role) {
        return $role . '+' . $timestamp . '@' . config('app.demo.email_suffix');
    }

    public static function generateNameFromEmail($email){
        $emailParts = explode('@', $email);
        return ucfirst($emailParts[0]);
    }
}
