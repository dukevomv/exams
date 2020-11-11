<?php

namespace App\Services;
use App\Enums\UserRole;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Test;
use Auth;
use Illuminate\Support\Arr;
use phpDocumentor\Reflection\Types\Integer;

class TestService implements TestServiceInterface {

    /**
     * @var \App\Models\Test
     */
    private $test;

    public function __construct(Test $test){
        $this->test = $test;
    }

    public function get(array $params = []){
        $tests = Test::withCount('segments')->whereIn('lesson_id', $this->getApprovedLessonIds());

        if(!is_null(Arr::get($params,'lesson',null)))
            $tests->where('lesson_id',Arr::get($params,'lesson'));

        if(!is_null(Arr::get($params,'search',null)))
            $tests->search(Arr::get($params,'search'));

        switch (Auth::user()->role){
            case UserRole::STUDENT:
                $tests->where('status','!=','draft');
                break;
            default:
                break;
        }

        return is_null(Arr::get($params,'paginate',null)) ? $tests->get() : $tests->paginate(10);
    }

    public function fetchById($id){
        return Test::with('segments.tasks','users','user')->where('id',$id)->whereIn('lesson_id', $this->getApprovedLessonIds())->firstOrFail();
    }

    public function calculateUserPoints(Test $test, $userId){
        $test->mergeUserAnswersToTest($userId);
        //todo calculate points based on correct answers and user's answers
        return $test;
    }

    private function getApprovedLessonIds(){
        return Lesson::approved()->get()->pluck('id')->all();
    }
}
