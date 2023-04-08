<?php

namespace App\Util;

use Illuminate\Support\Facades\Session;

class Demo {

    public const DEMO = 'demo';
    public const TRIAL = 'trial';
    public const MODES = [self::DEMO,self::TRIAL];
    public const IDENTIFIER_FIELDS = [
        'demo' => 'demo_user_id',
        'trial' => 'trial_id'
    ];

    /**
     * @param $timestamp
     * @param $role
     *
     * @return string
     */
    public static function generateEmailForRole($timestamp, $role) {
        return $role . '+' . $timestamp . '@' . config('app.demo.email_suffix');
    }

    public static function generateNameFromEmail($email) {
        $emailParts = explode('@', $email);
        return ucfirst($emailParts[0]);
    }

    public static function shouldSendMails() {
        $mode = self::getModeFromSessionIfAny();
        return is_null($mode) || $mode !== Demo::DEMO;
    }

    public static function getModeFromSessionIfAny(){
        $mode = null;
        foreach (self::MODES as $type) {
            if (config('app.'.$type.'.enabled')
                && (Session::has(config('app.' . $type . '.session_field'))
                    || Session::has(config('app.' . $type . '.session_guest_field')))) {
                $mode = $type;
                break;
            }
        }
        return $mode;
    }

    public static function getSessionValueOfMode($mode){
        if(Session::has(config('app.'.$mode.'.session_field'))){
            return Session::get(config('app.'.$mode.'.session_field'));
        }elseif(Session::has(config('app.'.$mode.'.session_guest_field'))){
            return Session::get(config('app.'.$mode.'.session_guest_field'));
        }
        return null;
    }

    public static function shouldBeAbleToSwitchRole($role = null): bool {
        return config('app.demo.enabled')
            && Session::has(config('app.demo.session_field'));
    }

    public static function shouldShowModeBanner($mode): bool {
        return config('app.'.$mode.'.enabled')
            && Session::has(config('app.'.$mode.'.session_field'));
    }
}
