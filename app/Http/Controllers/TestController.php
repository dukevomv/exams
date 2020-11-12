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

        $timer = $this->service->calculateTimer($test);

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
