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
		$test = Test::with('segments')->where('id',$id)->whereIn('lesson_id',$lessons)->first();
		return view('tests.preview',['test'=>$test]);
	}

	public function lobby($id = null) {
		$test = Test::where('id',$id)->where('status','!=','draft')->with('lesson','users')->first();
		if(is_null($test))
			return redirect('tests');
		return view('tests.lobby',['test'=>$test]);
	}
}
