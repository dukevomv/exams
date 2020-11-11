<?php

namespace App\Http\Controllers;
use App\Enums\General;
use App\Services\TestServiceInterface;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Test;
use Carbon\Carbon;
use Log;
use Auth;

class TestController extends Controller
{
    protected $service;

    public function __construct(TestServiceInterface $service) {
        $this->service = $service;
    }

	public function index(Request $request) {
		$lessons = Lesson::approved()->get();
		$filters = $request->only(['search','lesson']);
		$filters['paginate'] = General::DEFAULT_PAGINATION;

		return view('tests.index',[
		    'tests' => $this->service->get($filters),
            'lessons' => $lessons
        ]);
	}

	public function preview($id = null, Request $request) {
		$test = $this->service->fetchById($id);
        $test->mergeMyAnswersToTest();

        //todo
        //$test = $this->service->calculateTimer($id);
		$seconds_gap = 30;
		$timer = [
			'running' => false,
			'remaining_seconds' => $test->duration*60,
			'actual_time' => false,
			'seconds_gap' => $seconds_gap
		];
		
		$now = Carbon::now();
		switch ($test->status) {
			case 'started':
				$timer['running']  = true;
				$actually_started = Carbon::parse($test->started_at);
				$button_pressed = $actually_started->copy()->subSeconds($seconds_gap);
				$should_finish = $actually_started->copy()->addMinutes($test->duration);
				if($now->gte($actually_started)){
					$timer['actual_time']  = true;
					if($now->lte($should_finish)){
						$timer['remaining_seconds'] = $now->diffInSeconds($should_finish);
					} else {
						$timer['remaining_seconds'] = 0;
						$timer['running']  = false;
					}
				} else {
					$timer['remaining_seconds'] = $now->diffInSeconds($actually_started);
				}
				break;
			case 'finished':
				$timer['running']  = true;
				$actually_finished = Carbon::parse($test->finished_at);
				$button_pressed = $actually_finished->copy()->subSeconds($seconds_gap);
				if($now->gte($actually_finished)){
					$timer['remaining_seconds'] = 0;
					$timer['running']  = false;
					$timer['actual_time']  = true;
				} else {
					$timer['remaining_seconds'] = $now->diffInSeconds($actually_finished);
					$timer['actual_time']  = false;
				}
				break;
			case 'published':
			case 'graded':
			default:
				// code...
				break;
		}
		return view('tests.preview',[
			'test' => $test,
			'timer' => $timer,
			'now' => Carbon::now(),
		]);
	}

	public function lobby($id = null) {
		$test = Test::where('id',$id)->where('status','!=','draft')->with('lesson','users')->first();
		if(is_null($test))
			return redirect('tests');
		return view('tests.lobby',['test'=>$test]);
	}
}
