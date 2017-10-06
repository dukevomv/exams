<?php

namespace App\Http\Controllers\Professor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Segments\Segment;
use App\Models\Lesson;
use Log;

class SegmentController extends Controller
{
	public function index(Request $request) {
		$segments = Segment::all();

		$lessons = Lesson::approved()->get();
/*
		if($request->input('status','') != ''){
			if($request->status == 'approved'){
				$lessons->whereHas('status',function($query){
					$query->where('approved',1);
				});
			} else if($request->status == 'pending'){
				$lessons->whereHas('status',function($query){
					$query->where('approved',0);
				});
			} else if($request->status == 'unsubscribed'){
				$lessons->has('status','=',0);
			}
		}	

		$lessons = $lessons->paginate(10);*/
		return view('segments.index',['segments'=>$segments,'lessons'=>$lessons]);
	}

	public function createView(Request $request) {
		$lessons = Lesson::approved()->get();
		return view('segments.create',['lessons'=>$lessons]);
	}

	public function update(Request $request) {
		if(!is_null($request->segment_id)){
			$segment = Segment::find($request->segment_id);
			$segment->fill($request->only(['lesson_id','title','description']));
		}	else {
			$segment = Segment::create($request->only(['lesson_id','title','description']));
		}
/*
		foreach($request->tasks)
		$segment->tasks()->save();*/

		return $segment;
	}
}
