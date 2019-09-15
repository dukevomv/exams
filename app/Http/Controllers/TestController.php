<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Segments\Segment;
use App\Models\Lesson;
use App\Models\Test;

use Carbon\Carbon;
use Log;
use Auth;

class TestController extends Controller
{
	public function index(Request $request) {
		$lessons 	= Lesson::approved()->get();
		$tests = Test::withCount('segments')->whereIn('lesson_id',$lessons->pluck('id')->all());

		if($request->input('lesson','') != '')
			$tests->where('lesson_id',$request->lesson);
		
		if($request->input('search','') != '')
			$tests->search($request->search);

		if(Auth::user()->role == 'student')
			$tests = $tests->where('status','!=','draft');
			
		$tests = $tests->paginate(10);
		return view('tests.index',['tests'=>$tests,'lessons'=>$lessons]);
	}

	public function preview($id = null, Request $request) {
		$lessons = Lesson::approved()->get()->pluck('id')->all();
		$test = Test::with('segments.tasks','users')->where('id',$id)->whereIn('lesson_id',$lessons)->first();
		
		$remaining_seconds = Carbon::now()->diffInSeconds(Carbon::parse($test->started_at));
		$seconds_gap = 30;
		$actual_time = false;
		if($remaining_seconds>$seconds_gap){
			$remaining_seconds = Carbon::now()->diffInSeconds(Carbon::parse($test->started_at)->addMinutes($test->duration)->addSeconds($seconds_gap));
			$actual_time = true;
		}
		
		
		return view('tests.preview',[
			'test'=>$test,
			'now'=>Carbon::now()->toDateTimeString(),
			'remaining_seconds' => $remaining_seconds,
			'actual_time' => $actual_time,
			'seconds_gap' => $seconds_gap
		]);
	}

	public function lobby($id = null) {
		$test = Test::where('id',$id)->where('status','!=','draft')->with('lesson','users')->first();
		if(is_null($test))
			return redirect('tests');
		return view('tests.lobby',['test'=>$test]);
	}
}
