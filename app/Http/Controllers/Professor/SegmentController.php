<?php

namespace App\Http\Controllers\Professor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Segments\Segment;
use App\Models\Lesson;

class SegmentController extends Controller
{
	public function index(Request $req) {
		$segments = Segment::all();

		$lessons = Lesson::approved()->get();
/*
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

		$lessons = $lessons->paginate(10);*/
		return view('segments.index',['segments'=>$segments,'lessons'=>$lessons]);
	}

	public function createView(Request $req) {
		$lessons = Lesson::approved()->get();
		return view('segments.create',['lessons'=>$lessons]);
	}
}
