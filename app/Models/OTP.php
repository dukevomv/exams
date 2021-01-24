<?php

namespace App\Models;

use App\Mail\OTP as OTPMail;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class OTP extends Model {

    const MAX_TRIES                    = 5;
    const EXPIRATION_PERIOD_IN_MINUTES = 5;

    public $table    = 'otp';
    public $fillable = ['value', 'email', 'expires_at', 'tries'];

    private static function getUserFromEmail($email) {
        return User::where('email', $email)->first();
    }

    public static function generateForMail($email, $sendMail = true) {
        $user = self::getUserFromEmail($email);
        $object = self::firstOrNew(['email' => $email]);
        $object->generateNewCode();
        $object->save();
        $user->otp_pending = true;
        $user->save();

        if ($sendMail) {
            Mail::to($email)->send(new OTPMail($object->value));
        }
        return $object;
    }

    public function generateNewCode() {
        $this->value = mt_rand(100000, 999999);
        $this->expires_at = Carbon::now()->addMinutes(self::EXPIRATION_PERIOD_IN_MINUTES);
        $this->tries = 0;
        $this->save();
    }

    public static function confirmForEmail($value, $email) {
        $user = self::getUserFromEmail($email);
        $otp = self::where(['email' => $email])->first();
        if (!is_null($otp)) {
            if ($otp->value === $value) {
                if (Carbon::now()->lte($otp->expires_at) && $otp->tries < self::MAX_TRIES) {
                    $user->otp_pending = false;
                    $user->save();
                    return ['success' => 'You have successfully logged in'];
                } else {
                    return ['error' => 'Your OTP has expired. Send a new one by clicking "Send again"'];
                }
            } else {
                $otp->tries++;
                $otp->save();
                return ['error' => 'The OTP you\'ve entered was incorrect. Check your email and try again.'];
            }
        }
    }
}
