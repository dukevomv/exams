<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Lesson;
use Log;

class LessonController extends Controller
{
	public function index(Request $request) {
		$lessons = Lesson::with('status');

		if($request->input('status','') != '')
			$lessons->{$request->status}();
		
		if($request->input('search','') != '')
			$lessons->search($request->search);

		$lessons = $lessons->paginate(10);
		
		return view('lessons.index',['lessons'=>$lessons]);
	}
}
