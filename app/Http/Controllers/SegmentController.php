<?php

namespace App\Http\Controllers;
use App\Enums\TaskType;
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
		
		if($request->input('search','') != '')
			$segments->search($request->search);

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
		// return view('segments')->with(['error'=>'Segment cannot be deleted.']);
		$lessons = Lesson::approved()->get();
		$segment = Segment::where('id',$id)->withTaskAnswers()->first();
		if(!is_null($id) && is_null($segment))
			return redirect('segments/create');
			
		return view('segments.update',['lessons'=>$lessons,'segment'=>$segment]);
	}

	public function update(Request $request) {
		$this->validate($request, [
	      'lesson_id' 			=> 'required|exists:lessons,id',
	      'title' 				=> 'required|string',
	      'description' 		=> 'required|string',
	      'tasks' 				=> 'array',
	      'tasks.*.type' 		=> 'required|string',
	      'tasks.*.points' 		=> 'required|integer|max:255',
	      'tasks.*.description' => 'required|string',
	      'tasks.*.position' 	=> 'required|integer|max:255',
	    ]);
	$segment = Segment::updateOrCreate(['id'=>$request->input('id',null)],$request->only(['lesson_id','title','description']));
    foreach($request->input('tasks',[]) as $req_task){
			$task = $segment->tasks()->updateOrCreate(
				['id'=>isset($req_task['id']) ? $req_task['id'] : null],
				array_only($req_task,['type','position','description','points'])
			);
			if(isset($req_task['data']))
				$task_details = $this->fillTaskDetails($task,$req_task['data']);
    }
    
    $request->session()->flash('success', 'Segment saved successfully!');
    
		return $segment;
	}

	public function preview($id = null, Request $request) {
		$segment = Segment::where('id',$id)->with('lesson')->withTaskAnswers()->first();
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
            TaskType::RMC => ['description','points','correct'],
            TaskType::CMC => ['description','points','correct'],
            TaskType::FREE_TEXT => ['description'],
            TaskType::CORRESPONDENCE => ['side_a','side_b'],
            TaskType::CODE => []//todo fix this as task
		];
		$details = [];
		foreach($task_data as $option){
			$details[] = $task->{$task->type}()->updateOrCreate(['id'=>isset($option['id']) ? $option['id'] : null],array_only($option,$task_type_keys[$task->type]));
		}
		return $details;
	}
}
