<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Lesson;
use App\Models\OTP;
use App\Models\Segments\Segment;
use App\Models\Test;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Session;

class HomeController extends Controller {

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function home() {
        return view('home');
    }

    public function settings() {
        return view('settings');
    }

    public function updateSettings(Request $request) {
        $user = Auth::user();
        $this->validate($request, [
            'email'       => [
                'required',
                'string',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'name'        => 'required|string|max:255',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect('settings');
    }

    public function updateOTPSetting(Request $request) {
        $user = Auth::user();
        $this->validate($request, [
            'otp_enabled' => 'string',
        ]);
        $user->otp_enabled = $request->input('otp_enabled', false) == 'on';
        $user->save();
        return redirect('settings');
    }

    public function test() {
        return view('test');
    }

    public function viewOTP() {
        return view('auth.otp');
    }

    public function resendOTP() {
        OTP::generateForMail(Auth::user()->mailable_email,true,Auth::user());
        return back()->with(['success' => 'A new OTP has been sent to your email.']);
    }

    public function submitOTP(Request $request) {
        $this->validate($request, [
            'otp' => 'required',
        ]);
        $results = OTP::confirmForEmail($request->otp, Auth::user()->mailable_email,Auth::user());
        if (array_key_exists('success', $results)) {
            return redirect('/');
        } else {
            return back()->with($results);
        }
    }

    public function statistics() {
        $data = [
            'lessons'  => Lesson::count(),
            'tests'    => Test::count(),
            'segments' => Segment::count(),
            'users'    => [
                'total'     => User::count(),
                'admin'     => User::where('role', UserRole::ADMIN)->count(),
                'professor' => User::where('role', UserRole::PROFESSOR)->count(),
                'student'   => User::where('role', UserRole::STUDENT)->count(),
            ],
        ];
        return view('statistics', $data);
    }
}
