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

	public function updateView($id = null, Request $request) {
		$lessons = Lesson::approved()->get();
		$segment = Segment::find($id);
		if(!is_null($id) && is_null($segment))
			return redirect('segments/create');
		return view('segments.update',['lessons'=>$lessons,'segment'=>$segment]);
	}

	public function update(Request $request) {
		$segment = Segment::updateOrCreate(['id'=>$request->input('id',null)],$request->only(['lesson_id','title','description']));

    foreach($request->tasks as $req_task){
			$task = $segment->tasks()->updateOrCreate(['id'=>isset($req_task['id']) ? $req_task['id'] : null],array_only($req_task,['type','position','description','points']));
			$task_details = $this->fillTaskDetails($task,$req_task['data']);
    }

		return $segment;
	}

	private function fillTaskDetails($task,$task_data){
		$task_type_keys = [
			'rmc' => ['description','correct'],
			'cmc' => ['description','correct']
		];
		$details = [];
		foreach($task_data as $option){
			$details[] = $task->{$task->type}()->updateOrCreate(['id'=>isset($option['id']) ? $option['id'] : null],array_only($option,$task_type_keys[$task->type]));
		}
		return $details;
	}
}
