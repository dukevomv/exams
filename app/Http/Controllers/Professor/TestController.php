<?php

namespace App\Http\Controllers\Professor;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Segments\Segment;
use App\Models\Lesson;
use App\Models\Test;

use Carbon\Carbon;
use Log;

class TestController extends Controller
{
	public function updateView($id = null, Request $request) {
		$lessons = Lesson::approved()->get();
		$test = Test::where('id',$id)->with(['segments'=>function($q){
			$q->withCount('tasks');
		}])->first();
		
		if(!is_null($id) && is_null($test))
			return redirect('tests/create');
			
		return view('tests.update',['lessons'=>$lessons,'test'=>$test]);
	}

	public function update(Request $request) {
		$this->validate($request, [
	      'lesson_id' 		=> 'required|exists:lessons,id',
	      'name' 					=> 'required|string',
	      'description' 	=> 'required|string',
	      'status' 				=> 'required|string|in:draft,published',
	      'scheduled_at'	=> 'required_if:status,published|nullable|date_format:Y-m-d\TH:i|after:today',
	      'duration'			=> 'nullable|integer',
	      'tasks' 				=> 'array',
	      'tasks.*' 			=> 'required|integer|segments,id',
	    ]);
    
		$fields = $request->only(['lesson_id','name','description','scheduled_at','duration','status']);
		if(array_key_exists('scheduled_at',$fields) && !is_null($fields['scheduled_at']))
			$fields['scheduled_at'] = Carbon::createFromFormat('Y-m-d\TH:i',$fields['scheduled_at']);

		$test = Test::updateOrCreate(['id'=>$request->input('id',null)],$fields);
		$ordered_segments = [];
		$count = 1;
	    foreach($request->input('segments',[]) as $req_segment){
				$ordered_segments[$req_segment] = ['position'=>$count];
				$count++;
	    }
	    $test->segments()->sync($ordered_segments);

		return $test;
	}

	public function delete($id = null) {
		$test = Test::where('id',$id)->first();
		if(is_null($id) || is_null($test))
			return back()->with(['error'=>'Test cannot be deleted.']);
		$test->delete();
		return back()->with(['success'=>'Test deleted successfully']);
	}

	public function start($id = null, Request $request) {
		$test = Test::where('id',$id)
						->where('status','published')
						->with('users')->first();
		
		if(is_null($test))
			return back()->with(['error'=>'You can not start this test.']);
		if(!Carbon::parse($test->scheduled_at)->isToday())
			return back()->with(['error'=>'This test can not start today.']);
		$registered_users = $test->users->filter(function ($value, $key) {return $value->pivot->status == 'registered';});
		if(count($registered_users) == 0)
			return back()->with(['error'=>'This test require registered users to start.']);
		$test->start();
		if($request->wantsJson())
			return response()->json($test);
		else
			return redirect('tests/'.$id);
	}

	public function finish($id = null, Request $request) {
		$test = Test::where('id',$id)
						->where('status','started')
						->first();
		if(is_null($test))
			return back()->with(['error'=>'You can not finish this test.']);
		$test->finish();
		if($request->wantsJson())
			return response()->json($test);
		else
			return redirect('tests/'.$id);
	}
}
