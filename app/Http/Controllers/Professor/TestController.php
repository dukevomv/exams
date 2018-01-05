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
		$test = Test::where('id',$id)->with(['segments'=>function($q){
			$q->withCount('tasks');
		}])->first();
		if(!is_null($id) && is_null($test))
			return redirect('tests/create');
		return view('tests.update',['lessons'=>$lessons,'test'=>$test]);
	}

	public function update(Request $request) {
		$fields = $request->only(['lesson_id','name','description','scheduled_at','duration']);
		if(array_key_exists('scheduled_at',$fields))
			$fields['scheduled_at'] = Carbon::createFromFormat('Y-m-d\Th:i',$fields['scheduled_at']);
		Log::info($fields);
		$test = Test::updateOrCreate(['id'=>$request->input('id',null)],$fields);
		$ordered_segments = [];
		$count = 1;
    foreach($request->segments as $req_segment){
			$ordered_segments[$req_segment] = ['position'=>$count];
			$count++;
    }
    $test->segments()->sync($ordered_segments);

		return $test;
	}
}
