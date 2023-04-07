<?php

namespace App\Http\Controllers;

use App\Enums\General;
use App\Enums\TestStatus;
use App\Models\Lesson;
use App\Models\Test;
use App\Models\TestInvite;
use App\Scopes\OnlyTrialScope;
use App\Scopes\WithoutTrialScope;
use App\Services\TestServiceInterface;
use Auth;
use Illuminate\Http\Request;
use Log;

class TestController extends Controller {

    protected $service;

    public function __construct(TestServiceInterface $service) {
        $this->service = $service;
    }

    public function index(Request $request) {
        $lessons = Lesson::approved()->get();
        $filters = $request->only(['search', 'lesson', 'status']);
        $filters['paginate'] = General::DEFAULT_PAGINATION;

        return view('tests.index', [
            'tests'   => $this->service->get($filters),
            'lessons' => $lessons,
        ]);
    }

    public function preview($id, Request $request) {
        $test = $this->service->setById($id);

        $data = ['test' => $this->service->prepareForCurrentUser()];
        if ($test->status !== TestStatus::GRADED) {
            $data['timer'] = $this->service->calculateTimer($test);
        }

        return view('tests.preview', $data);
    }

    public function lobby($id = null) {
        $test = Test::where('id', $id)->where('status', '!=', TestStatus::DRAFT)->with('lesson', 'users')->first();
        if (is_null($test)) {
            return redirect('tests');
        }
        return view('tests.lobby', ['test' => $test]);
    }

    public function previewInvitation($testId,$inviteUuid) {
        $invite = TestInvite::withoutGlobalScope(WithoutTrialScope::class)->withoutGlobalScope(OnlyTrialScope::class)->where('uuid',$inviteUuid)->where('test_id',$testId)->notifiedOnly()->first();
        $test = Test::withoutGlobalScope(WithoutTrialScope::class)->withoutGlobalScope(OnlyTrialScope::class)->find($testId);

        if(is_null($invite) || is_null($test)){
            Bugsnag::notifyException(new \InvalidArgumentException("Test or invite was not found"));
            abort(404,'Invitation not found');
        }
        if(!Auth::guest() && !UserIs::withPendingOTP(Auth::user())){
            if(Auth::user()->email === $invite->unique_user_email){
                return redirect('tests')->with(['success' => 'You have accepted exam invitation.']);
            } else {
                abort(404,'Invitation not found');
            }
        }
        return view('tests.invitation_preview', ['invite' => $invite,'test'=>$test]);
    }

    public function acceptInvitation($testId,$inviteUuid,Request $request) {
        Session::forget(config('app.trial.session_guest_field'));
        $this->validate($request,[
            'email'=>'required|email'
        ]);
        $invite = TestInvite::withoutGlobalScope(WithoutTrialScope::class)->withoutGlobalScope(OnlyTrialScope::class)->where('uuid',$inviteUuid)->firstOrFail();
        if($invite->student_email !== $request->email){
            return back()->with(['error' => 'This email does not match with this invitation.']);
        }
        if(!is_null($invite->user_id)){
            return back()->with(['error' => 'This has already been accepted.']);
        }
        $invite->enableTrialSessionIfAny();
        $user = $invite->accept();
        Auth::login($user);
        return back()->with(['success' => 'Invitation Accepted.']);
    }

    public function sendLoginCodeForInvitation($testId,$inviteUuid,Request $request) {
        Session::forget(config('app.trial.session_guest_field'));
        $this->validate($request,[
            'email'=>'required|email'
        ]);
        $invite = TestInvite::withoutGlobalScope(WithoutTrialScope::class)->withoutGlobalScope(OnlyTrialScope::class)->where('uuid',$inviteUuid)->firstOrFail();
        if($invite->student_email !== $request->email){
            return back()->with(['error' => 'This email does not match with this invitation.']);
        }
        if(is_null($invite->user_id)){
            return back()->with(['error' => 'This invitation needs to be accepted first.']);
        }
        $invite->enableTrialSessionIfAny();
        $user = $invite->loginRequest();
        Auth::login($user);
        return redirect('otp')->with(['success' => 'Login Code has been sent for invitation.']);
    }

}
