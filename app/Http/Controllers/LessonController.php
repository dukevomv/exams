<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Lesson;
use Log;
use Auth;

class LessonController extends Controller
{
	public function index(Request $request) {
		$lessons = Lesson::with('status');
		
		if(Auth::user()->role == 'admin')
			$lessons->withCount(['pending_users','approved_professors','approved_students']);

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
	
	public function requestApproval($id = null) {
		$lesson = Lesson::where('id',$id)->with('status')->first();
		if(is_null($id) || is_null($lesson) || !is_null($lesson->status))
			return back()->with(['error'=>'Request for approval is not available for this Lesson']);
		$lesson->users()->attach(['user_id'=>Auth::user()->id]);
		return back()->with(['success'=>'Request for approval sent for this Lesson']);
	}
	
	public function cancelApproval($id = null) {
		$lesson = Lesson::where('id',$id)->with('status')->first();
		if(is_null($id) || is_null($lesson) || is_null($lesson->status) || $lesson->status->approved == 1)
			return back()->with(['error'=>'Cancellation of approval is not available for this Lesson']);
		$lesson->users()->detach(['user_id'=>Auth::user()->id]);
		return back()->with(['success'=>'Request for approval sent for this Lesson']);
	}
	
	public function getUserApprovals($id = null) {
		$lesson = Lesson::where('id',$id)->with('users')->first();
		return $lesson;
	}
	
	public function toggleApprove(Request $request) {
		$lesson = Lesson::where('id',$request->lesson_id)->first();
		$user = $lesson->users()->where('users.id',$request->user_id)->first();
		$user->pivot->approved = !$user->pivot->approved;
		$user->pivot->save();
		return $user;
	}
}
