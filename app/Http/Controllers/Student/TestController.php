<?php

namespace App\Http\Controllers\Student;

use App\Enums\TestStatus;
use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Services\TestServiceInterface;
use Auth;
use Illuminate\Http\Request;
use Log;

class TestController extends Controller {

    protected $service;

    public function __construct(TestServiceInterface $service) {
        $this->service = $service;
    }

    public function register($id = null) {
        $user_id = Auth::id();
        $test = Test::where('id', $id)
                    ->where('status', TestStatus::PUBLISHED)
                    ->with('users')
                    ->first();

        if (is_null($test)) {
            return back()->with(['error' => 'You can not register to this test.']);
        }
        if (!$test->can_register) {
            return back()->with(['error' => 'You can not register to this test yet.']);
        }
        if ($test->users->contains($user_id)) {
            return back()->with(['error' => 'Already registered to this test.']);
        }

        $test->register();
        return redirect('tests/' . $id)->with(['success' => 'Registered to this test.']);
    }

    public function leave($id = null) {
        $user = Auth::user();
        $test = Test::where('id', $id)
                    ->where('status', TestStatus::PUBLISHED)
                    ->whereHas('users', function ($q) use ($user) {
                        $q->where('user_id', $user->id)->where('status', 'registered');
                    })->first();

        if (is_null($test)) {
            return back()->with(['error' => 'Cannot leave from this test.']);
        }

        $test->leave();
        return redirect('tests/' . $id)->with(['success' => 'Left the test.']);
    }

    public function submit(Request $request, $id = null) {
        $this->validate($request, [
            'answers'        => 'required|array',
            'answers.*.id'   => 'required',
            'answers.*.type' => 'required|string',
            'final'          => 'required|integer|in:0,1',
        ]);

        $test = Test::find($id);
        if (is_null($test)) {
            return back()->with(['error' => 'You can not submit to this test.']);
        }

        $test->saveStudentsAnswers(Auth::id(), $request->answers, $request->final == 1);

        return redirect('tests/' . $id);
    }
}
