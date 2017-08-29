<?php

namespace App\Http\Controllers\Professor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Lesson;
use Log;

class LessonController extends Controller
{
	public function index(Request $req) {
		$lessons = Lesson::with('status');

		if($req->input('status','') != ''){
			$lessons->{$req->status}();
		}	

		$lessons = $lessons->paginate(10);
		
		return view('lessons.index',['lessons'=>$lessons]);
	}
}
