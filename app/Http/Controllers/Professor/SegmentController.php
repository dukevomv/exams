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
		$lessons 	= Lesson::approved()->get();
		$segments = Segment::withCount('tests')->whereIn('lesson_id',$lessons->pluck('id')->all());

		if($request->input('lesson','') != '')
			$segments->where('lesson_id',$request->lesson);

		$segments = $segments->paginate(10);
		return view('segments.index',['segments'=>$segments,'lessons'=>$lessons]);
	}

	public function sidebarIndex(Request $request) {
		$segments = Segment::whereIn('lesson_id',Lesson::approved()->get()->pluck('id')->all());

		if(!is_null($request->input('lesson_id',null)))
			$segments->where('lesson_id',$request->lesson_id);

		if($request->input('search','') != '')
			$segments->where('title','like','%'.$request->search.'%');

		$segments = $segments->withCount('tasks')->get();
		return response()->json($segments);
	}

	public function updateView($id = null, Request $request) {
		$lessons = Lesson::approved()->get();
		$segment = Segment::where('id',$id)->withTasksAnswers()->first();
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

	public function preview($id = null, Request $request) {
		$segment = Segment::where('id',$id)->with('lesson')->withTasksAnswers()->first();
		if(!is_null($id) && is_null($segment))
			return redirect('segments');

		if($request->input('modal',0) == 1)
			return view('segments.modal_preview',['segment'=>$segment])->render();
		else
			return view('segments.preview',['segment'=>$segment]);
	}

	public function delete($id = null) {
		$segment = Segment::withCount('tests')->where('id',$id)->first();
		if(is_null($id) || is_null($segment) || $segment->tests_count > 0)
			return back()->with(['error'=>'Segment cannot be deleted.']);
		$segment->delete();
		return back()->with(['success'=>'Segment deleted successfully']);
	}





	private function fillTaskDetails($task,$task_data){
		$task_type_keys = [
			'rmc' => ['description','points','correct'],
			'cmc' => ['description','points','correct']
		];
		$details = [];
		foreach($task_data as $option){
			$details[] = $task->{$task->type}()->updateOrCreate(['id'=>isset($option['id']) ? $option['id'] : null],array_only($option,$task_type_keys[$task->type]));
		}
		return $details;
	}
}
