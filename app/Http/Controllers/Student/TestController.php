<?php

namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Segments\Segment;
use App\Models\Lesson;
use App\Models\Test;

use Carbon\Carbon;
use Log;
use Auth;


class TestController extends Controller
{
	public function register($id = null) {
		$user_id = Auth::id();
		$test = Test::where('id',$id)
						->where('status','published')
						->with('users')
						->first();
						
		if(is_null($test))
			return back()->with(['error'=>'You can not register to this test.']);
		if(!$test->can_register)
			return back()->with(['error'=>'You can not register to this test yet.']);
		if($test->users->contains($user_id))
			return back()->with(['error'=>'Already registered to this test.']);
			
		$test->register();
		return redirect('tests/'.$id)->with(['success'=>'Registered to this test.']);
	}

	public function leave($id = null) {
		$user = Auth::user();
		$test = Test::where('id',$id)
						->where('status','published')
						->whereHas('users',function($q) use ($user){
							$q->where('user_id',$user->id)->where('status','registered');
						})->first();
		
		if(is_null($test))
			return back()->with(['error'=>'Cannot leave from this test.']);
			
		$test->leave();
		return redirect('tests/'.$id)->with(['success'=>'Left the test.']);
	}
	
	public function submit($id = null) {
		$user_id = Auth::id();
		$test = Test::where('id',$id)
						->where('status','started') //TODO this for tests that just ended wont work (for the +30seconds)
						->with('users')
						->first();
		if(is_null($test))
			return back()->with(['error'=>'You can not register to this test.']);
		return redirect('tests/'.$id);
	}
	
	public function submit_final($id = null) {
		$user_id = Auth::id();
		$test = Test::where('id',$id)
						->where('status','started')
						->with('users')
						->first();
		if(is_null($test))
			return back()->with(['error'=>'You can not register to this test.']);
		return redirect('tests/'.$id);
	}
}
