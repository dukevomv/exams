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
	
	public function show(Lesson $lesson,Request $request) {
		return $lesson;
	}
	
	public function update(Request $request) {
		$lesson = Lesson::updateOrCreate(['id'=>$request->input('id',null)],$request->only(['name','gunet_code','semester']));
		return redirect('lessons');
	}
	
	public function delete($id = null) {
		$lesson = Lesson::withCount('users')->where('id',$id)->first();
		if(is_null($id) || is_null($lesson) || $lesson->users_count > 0)
			return back()->with(['error'=>'Lesson cannot be deleted.']);
		$lesson->delete();
		return back()->with(['success'=>'Lesson deleted successfully']);
	}
}
