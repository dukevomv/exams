<?php

namespace App\Services;

use App\Enums\TaskType;
use App\Enums\TestStatus;
use App\Enums\UserRole;
use App\Models\Lesson;
use App\Models\Test;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class TestService implements TestServiceInterface {

    /**
     * @var \App\Models\Test
     */
    private $test;

    public function __construct(Test $test) {
        $this->test = $test;
    }

    public function get(array $params = []) {
        $tests = Test::withCount('segments')->whereIn('lesson_id', $this->getApprovedLessonIds());

        if (!is_null(Arr::get($params, 'lesson', null))) {
            $tests->where('lesson_id', Arr::get($params, 'lesson'));
        }

        if (!is_null(Arr::get($params, 'search', null))) {
            $tests->search(Arr::get($params, 'search'));
        }

        switch (Auth::user()->role) {
            case UserRole::STUDENT:
                $tests->where('status', '!=', TestStatus::DRAFT);
                break;
            default:
                break;
        }

        return is_null(Arr::get($params, 'paginate', null)) ? $tests->get() : $tests->paginate(10);
    }

    public function fetchById($id) {
        return Test::with('segments.tasks', 'users', 'user')
                   ->where('id', $id)
                   ->whereIn('lesson_id', $this->getApprovedLessonIds())
                   ->withSegmentTaskAnswers()
                   ->firstOrFail();
    }


    public function calculateUserPoints(Test $test, $userId) {
        $test->mergeUserAnswersToTest($userId);

        for ($s = 0; $s < count($test->segments); $s++) {
            for ($t = 0; $t < count($test->segments[$s]->tasks); $t++) {
                $type = $test->segments[$s]->tasks[$t]->type;
                $points = $test->segments[$s]->tasks[$t]->points;
                $given_points = 0;
                switch ($type) {
                    case TaskType::RMC:
                        //FULL points are given if correct option is selected
                        //0 points are given if any wrong option is selected
                        for ($o = 0; $o < count($test->segments[$s]->tasks[$t]->{$type}); $o++) {
                            $isCorrect = $test->segments[$s]->tasks[$t]->{$type}[$o]->correct == 1;
                            $isSelected = $test->segments[$s]->tasks[$t]->{$type}[$o]->selected == 1;
                            if ($isCorrect && $isSelected) {
                                $given_points = $points;
                                break;
                            }
                        }
                        break;
                    case TaskType::CMC:
                        //FULL points are given if only all correct options are selected
                        //0 points are given if all options are selected
                        //formula = ( correct_selected_options * (points/total_correct_options) ) - ( wrong_selected_options * (points/total_wrong_options) )
                        //with no negative results
                        //this happens in order to 'punish' those who select all options regardless of correct/wrong

                        $count = count($test->segments[$s]->tasks[$t]->{$type});
                        $correctCount = $test->segments[$s]->tasks[$t]->{$type}->filter(function ($value, $key) {
                                return $value->correct == 1;
                            })->count();
                        $wrongCount = $count - $correctCount;

                        $precision = 100;

                        //making sure we wont divide something with 0 by accident and we always have integers to add or subtract
                        $correctPoints = ($correctCount == 0 ? 0 : round(+$precision * ($points/$correctCount))); //Positive multiplied with precision and rounded
                        $wrongPoints   = (  $wrongCount == 0 ? 0 : round(-$precision * ($points/$wrongCount)));   //Negative in order to subtract from positive points

                        for ($o = 0; $o < count($test->segments[$s]->tasks[$t]->{$type}); $o++) {
                            $isCorrect = $test->segments[$s]->tasks[$t]->{$type}[$o]->correct == 1;
                            $isSelected = $test->segments[$s]->tasks[$t]->{$type}[$o]->selected == 1;
                            if ($isSelected) {
                                $option_points = $isCorrect ? $correctPoints : $wrongPoints;
                                $given_points += $option_points;
                                $test->segments[$s]->tasks[$t]->{$type}[$o]->given_points = $option_points/$precision;
                            }
                        }
                        $given_points = $given_points/$precision;
                        break;
                }
                //Making sure no negative grading will be applied to the task
                $test->segments[$s]->tasks[$t]->given_points = $given_points < 0 ? 0 : $given_points;
            }
        }
        return $test;
    }

    public function calculateTimer(Test $test) {
        $seconds_gap = 30;
        $timer = [
            'running'           => false,
            'remaining_seconds' => $test->duration * 60,
            'actual_time'       => false,
            'seconds_gap'       => $seconds_gap,
        ];

        $now = Carbon::now();
        switch ($test->status) {
            case TestStatus::STARTED:
                $timer['running'] = true;
                $actually_started = Carbon::parse($test->started_at);
                $button_pressed = $actually_started->copy()->subSeconds($seconds_gap);
                $should_finish = $actually_started->copy()->addMinutes($test->duration);
                if ($now->gte($actually_started)) {
                    $timer['actual_time'] = true;
                    if ($now->lte($should_finish)) {
                        $timer['remaining_seconds'] = $now->diffInSeconds($should_finish);
                    } else {
                        $timer['remaining_seconds'] = 0;
                        $timer['running'] = false;
                    }
                } else {
                    $timer['remaining_seconds'] = $now->diffInSeconds($actually_started);
                }
                break;
            case TestStatus::FINISHED:
                $timer['running'] = true;
                $actually_finished = Carbon::parse($test->finished_at);
                $button_pressed = $actually_finished->copy()->subSeconds($seconds_gap);
                if ($now->gte($actually_finished)) {
                    $timer['remaining_seconds'] = 0;
                    $timer['running'] = false;
                    $timer['actual_time'] = true;
                } else {
                    $timer['remaining_seconds'] = $now->diffInSeconds($actually_finished);
                    $timer['actual_time'] = false;
                }
                break;
            default:
                // code...
                break;
        }
        return $timer;
    }

    private function getApprovedLessonIds() {
        return Lesson::approved()->get()->pluck('id')->all();
    }

    public function prepareForUser(Test $test){
        switch (Auth::user()->role){
            case UserRole::STUDENT:
                $test = $test->mergeMyAnswersToTest();
                break;
        }
        //todo make this a resource since you have hidden fields and you dont want them in your results
        //hidden is returned in payload but it needs to be serialized in order not to be included ->toArray()
        return $test;
    }
}
