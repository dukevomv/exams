<?php

namespace App\Http\Controllers\Professor;

use App\Enums\TestStatus;
use App\Enums\TestUserStatus;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Test;
use App\Services\TestServiceInterface;
use App\Util\Points;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
            'duration'     => 'nullable|integer',
            'tasks'        => 'array',
            'tasks.*'      => 'required|integer|segments,id',
        ]);

        $fields = $request->only(['lesson_id', 'name', 'description', 'scheduled_at', 'duration', 'status']);
        if($fields['status'] == TestStatus::DRAFT){
            $fields['scheduled_at'] = null;
        }elseif ($fields['status'] == TestStatus::PUBLISHED && array_key_exists('scheduled_at', $fields) && !is_null($fields['scheduled_at'])) {
            $fields['scheduled_at'] = Carbon::createFromFormat('Y-m-d\TH:i', $fields['scheduled_at']);
        }

        return $this->service->updateOrCreate($request->input('id', null),$fields,$request->input('segments', []));
    }

    public function delete($id = null) {
        $test = Test::where('id', $id)->first();
        if (is_null($id) || is_null($test)) {
            return back()->with(['error' => 'Test cannot be deleted.']);
        }
        $test->delete();
        return back()->with(['success' => 'Test deleted successfully']);
    }

    public function start($id = null, Request $request) {
        $test = Test::where('id', $id)
                    ->where('status', TestStatus::PUBLISHED)
                    ->with('users')->first();

        if (is_null($test)) {
            abort(400,'You can not start this test.');
        }
//        if (!Carbon::parse($test->scheduled_at)->isToday()) {
//            return back()->with(['error' => 'This test can not start today.']);
//        }
        $registered_users = $test->users->filter(function ($value, $key) {
            return $value->pivot->status == 'registered';
        });
        if (count($registered_users) == 0) {
            abort(400,'This test require registered users to start.');
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
            abort(400,'You can not finish this test.');
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

        return view('tests.preview', [
            'test'    => $this->service->prepareForCurrentUser(),
            'forUser' => $userId,
        ]);
    }

    public function autoGrade($id, $userId) {
        //saves task points that haven't been saved yet
        $this->service->setById($id);
        $this->service->autoGradeForUser($userId);
        return back();
    }

    public function autoCalculateGrades($id) {
        //todo make sure db value is valid!!!!!!
        //todo make sure tests with non calc can not auto grade everyone at once
        //make sure test is not graded already
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
        //todo the below line is to set the forUserId for the student is being graded
        $this->service->calculateUserPoints($userId);
        $this->service->gradeUserTask($request->only('task_id', 'points'));
        return back();
    }

    public function publishGrade($id, $userId, Request $request) {
        $test = $this->service->fetchById($id);
        //todo make sure grades are publishable
        $test->publishProfessorGrade($userId);
        return [];
    }

    public function exportCSV($id, Request $request) {
        $test = $this->service->setById($id);

        $filename = $test->name.' - '.Carbon::now()->toDateString();
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=".$filename.".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Student ID','Student Name','Grade','Total','Percentage');
        $students = [];
        foreach($this->service->toArrayUsers() as $st){
            if($st['status'] === TestUserStatus::GRADED){
                $percentage = Points::getPercentage($st['given_points'],$st['total_points']);
                $students[] = [$st['id'],$st['name'],$st['given_points'],$st['total_points'],$percentage];
            }
        }

        $callback = function() use ($students, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach($students as $s) {
                fputcsv($file, $s);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
