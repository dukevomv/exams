<?php

namespace App\Http\Controllers\Professor;

use App\Enums\TestStatus;
use App\Enums\TestUserStatus;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Test;
use App\Models\TestInvite;
use App\Notifications\StudentInvitedToTest;
use App\Services\TestServiceInterface;
use App\Util\Points;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Log;

class TestController extends Controller {

    protected $service;

    public function __construct(TestServiceInterface $service) {
        $this->service = $service;
    }

    public function updateView($id = null, Request $request) {
        $lessons = Lesson::approved()->get();
        $test = Test::where('id', $id)->with(['segments' => function ($q) {
            $q->withCount('tasks');
        },
        ])->first();

        if (!is_null($id) && is_null($test)) {
            return redirect('tests/create');
        }

        return view('tests.update', ['lessons' => $lessons, 'test' => $test]);
    }

    public function update(Request $request) {
        $this->validate($request, [
            'lesson_id'    => 'required|exists:lessons,id',
            'name'         => 'required|string',
            'description'  => 'required|string',
            'status'       => 'required|string|in:' . TestStatus::DRAFT . ',' . TestStatus::PUBLISHED,
            'scheduled_at' => 'required_if:status,' . TestStatus::PUBLISHED . '|nullable|date_format:Y-m-d\TH:i|after:today',
            'duration'     => 'nullable|integer|max:500',
            'tasks'        => 'array',
            'tasks.*'      => 'required|integer|segments,id',
        ]);

        $fields = $request->only(['lesson_id', 'name', 'description', 'scheduled_at', 'duration', 'status']);
        if ($fields['status'] == TestStatus::DRAFT) {
            $fields['scheduled_at'] = null;
        } elseif ($fields['status'] == TestStatus::PUBLISHED && array_key_exists('scheduled_at', $fields) && !is_null($fields['scheduled_at'])) {
            $fields['scheduled_at'] = Carbon::createFromFormat('Y-m-d\TH:i', $fields['scheduled_at']);
        }

        return $this->service->updateOrCreate($request->input('id', null), $fields, $request->input('segments', []));
    }

    public function inviteStudentsList($testId) {
        $test = Test::where('id', $testId)->where('status', TestStatus::PUBLISHED)->first();
        if (is_null($test)) {
            return redirect('tests');
        }
        return view('tests.invites', ['testId' => $test->id, 'invites' => $test->invites()->orderBy('student_name','asc')->paginate(25)]);
    }

    public function inviteStudents($testId,Request $request) {
        $this->validate($request, [
            'student_name'=> 'required|string',
            'student_email'=> 'required|string|email',
            'send_invite'=> 'sometimes',
        ]);

        $test = Test::findOrFail($testId);
        if($test->invites()->where('student_email',$request->input('student_email'))->count() > 0){
            return back()->with(['error' => 'Student email already exists in the list.']);
        }
        $invite = $test->invites()->create($request->only(['student_name','student_email']));

        if($request->input('send_invite','off') === 'on'){
            $invite->notify(new StudentInvitedToTest($test));
        }
        return back()->with(['success' => 'Student added in invitation list successfully']);
    }

    public function sendInvitation($testId,$id) {
        $invite = TestInvite::findOrFail($id);
        if($invite->notifications()->count() == 0){
            $invite->notify(new StudentInvitedToTest($invite->test));
        }
        return back()->with(['success' => 'Student invited']);
    }

    public function removeInvitedStudent($testId,$id,Request $request) {
        $test = Test::findOrFail($testId);
        $invite = $test->invites()->find($id);
        if(is_null($invite)){
            return back()->with(['error' => 'Entry was not found on the list']);
        }
        if($invite->status === TestInvite::ACCEPTED){
            return back()->with(['error' => 'You can not delete an accepted invitation']);
        }
        $invite->delete();
        return back()->with(['success' => 'Student removed from invitation list']);
    }

    public function delete($id = null) {
        $test = Test::where('id', $id)->first();
        if (is_null($id) || is_null($test)) {
            return back()->with(['error' => 'Test cannot be deleted']);
        }
        $test->delete();
        return back()->with(['success' => 'Test deleted successfully']);
    }

    public function start($id = null, Request $request) {
        $test = Test::where('id', $id)
                    ->where('status', TestStatus::PUBLISHED)
                    ->with('users')->first();

        if (is_null($test)) {
            abort(400, 'You can not start this test.');
        }
//        if (!Carbon::parse($test->scheduled_at)->isToday()) {
//            return back()->with(['error' => 'This test can not start today.']);
//        }
        $registered_users = $test->users->filter(function ($value, $key) {
            return $value->pivot->status == 'registered';
        });
        if (count($registered_users) == 0) {
            abort(400, 'This test require registered users to start.');
        }
        $test->start();
        if ($request->wantsJson()) {
            return response()->json($test);
        } else {
            return redirect('tests/' . $id);
        }
    }

    public function finish($id = null, Request $request) {
        $test = Test::where('id', $id)
                    ->where('status', TestStatus::STARTED)
                    ->first();
        if (is_null($test)) {
            abort(400, 'You can not finish this test.');
        }
        $test->finish();
        if ($request->wantsJson()) {
            return response()->json($test);
        } else {
            return redirect('tests/' . $id);
        }
    }

    public function userPreview($id, $userId, Request $request) {
        $test = $this->service->setById($id);
        if (in_array($test->status, [TestStatus::STARTED, TestStatus::PUBLISHED])) {
            return redirect('/tests/' . $id);
        }
        $this->service->calculateUserPoints($userId);
        $data = [
            'test'    => $this->service->prepareForCurrentUser(),
            'forUser' => $userId,
        ];
        $data['is_professor'] = Auth::user()->role === \App\Enums\UserRole::PROFESSOR;
        $data['is_student'] = Auth::user()->role === \App\Enums\UserRole::STUDENT;

        $data['professor_for_student'] = $data['is_professor']
            && isset($data['test']['for_student']);
        $data['professor_for_student_not_participated'] = $data['professor_for_student']
            && in_array($data['test']['for_student']['status'],
                [
                    \App\Enums\TestUserStatus::LEFT,
                    \App\Enums\TestUserStatus::REGISTERED
                ]);
        $data['show_segments'] = $data['professor_for_student']
                && in_array($data['test']['status'], [
                    \App\Enums\TestStatus::FINISHED,
                    \App\Enums\TestStatus::GRADED
                ]);

        return view('tests.preview',$data);
    }

    public function autoGrade($id, $userId) {
        //saves task points that haven't been saved yet
        $this->service->setById($id);
        $this->service->autoGradeForUser($userId);
        return back();
    }

    public function autoCalculateGrades($id) {
        //todo|debt - make sure tests with non calc can not auto grade everyone at once and test is not graded already
        $this->service->setById($id);
        $this->service->autoGradeUsers();
        return back();
    }

    public function publishGrades($id) {
        $this->service->setById($id);
        $this->service->publishTestGrades();
        return back();
    }

    public function gradeUserTask($id, $userId, Request $request) {
        $this->validate($request, [
            'task_id' => 'required|integer',
            'points'  => 'required|numeric',
        ]);

        $this->service->setById($id);
        //todo|debt - the below line is to set the forUserId for the student is being graded
        $this->service->calculateUserPoints($userId);
        $this->service->gradeUserTask($request->only('task_id', 'points'));
        return back();
    }

    public function publishGrade($id, $userId, Request $request) {
        $test = $this->service->fetchById($id);
        //todo|debt - make sure grades are publishable
        $test->publishProfessorGrade($userId);
        return [];
    }

    public function exportCSV($id, Request $request) {
        $test = $this->service->setById($id);

        $filename = $test->name . ' - ' . Carbon::now()->toDateString();
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $columns = ['Student ID', 'Student Name', 'Grade', 'Total', 'Percentage'];
        $students = [];
        foreach ($this->service->toArrayUsers() as $st) {
            if ($st['status'] === TestUserStatus::GRADED) {
                $percentage = Points::getPercentage($st['given_points'], $st['total_points']);
                $students[] = [$st['id'], $st['name'], $st['given_points'], $st['total_points'], $percentage];
            }
        }

        $callback = function () use ($students, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($students as $s) {
                fputcsv($file, $s);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
