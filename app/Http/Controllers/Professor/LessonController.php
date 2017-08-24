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
			if($req->status == 'approved'){
				$lessons->whereHas('status',function($query){
					$query->where('approved',1);
				});
			} else if($req->status == 'pending'){
				$lessons->whereHas('status',function($query){
					$query->where('approved',0);
				});
			} else if($req->status == 'unsubscribed'){
				$lessons->has('status','=',0);
			}
		}	

		$lessons = $lessons->paginate(10);
    return view('lessons.index',['lessons'=>$lessons]);
  }
}
