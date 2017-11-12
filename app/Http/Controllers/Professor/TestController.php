<?php

namespace App\Http\Controllers\Professor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Lesson;
use App\Models\Test;

class TestController extends Controller
{
	public function index(Request $request) {
		$lessons 	= Lesson::approved()->get();
		$tests = Test::whereIn('lesson_id',$lessons->pluck('id')->all());

		if($request->input('lesson','') != '')
			$tests->where('lesson_id',$request->lesson);

		$tests = $tests->paginate(10);
		return view('tests.index',['tests'=>$tests,'lessons'=>$lessons]);
	}

	public function updateView($id = null, Request $request) {
		$lessons = Lesson::approved()->get();
		$test = Test::where('id',$id)->first();
		if(!is_null($id) && is_null($test))
			return redirect('tests/create');
		return view('tests.update',['lessons'=>$lessons,'test'=>$test]);
	}

}
