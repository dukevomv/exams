<?php

namespace App\Util;

class FirebaseControl {

    public static function createOrUpdate($path,$payload) {
        if (config('services.firebase.enabled')) {
            $firebase = app('firebase');
            $firebase->update($payload,$path);
        }
    }
}
