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
    private $forUserId                   = null;
    private $includeUserAnswers          = false;
    private $includeCorrectAnswers       = false;
    private $includeUserCalculatedPoints = false;

    public function __construct(Test $test) {
        $this->test = $test;
    }

    public function forUserId($userId) {
        $this->forUserId = $userId;
        return $this;
    }

    public function withCorrectAnswers() {
        $this->includeCorrectAnswers = true;
        return $this;
    }

    public function withUserAnswers() {
        $this->includeUserAnswers = true;
        return $this;
    }

    public function withUserCalculatedPoints() {
        $this->includeUserCalculatedPoints = true;
        return $this;
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
        //make this as flags and calclulate everything in toArray methods
        $test = $this
            ->forUserId($userId)
            ->withCorrectAnswers()
            ->withUserAnswers()
            ->withUserCalculatedPoints()
            ->mergeUserAnswersToTest($test);

        for ($s = 0; $s < count($test->segments); $s++) {
            for ($t = 0; $t < count($test->segments[$s]->tasks); $t++) {
                $calculative = true;
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
                        $correctPoints = ($correctCount == 0 ? 0 : round(+$precision * ($points / $correctCount))); //Positive multiplied with precision and rounded
                        $wrongPoints = ($wrongCount == 0 ? 0 : round(-$precision * ($points / $wrongCount)));   //Negative in order to subtract from positive points

                        for ($o = 0; $o < count($test->segments[$s]->tasks[$t]->{$type}); $o++) {
                            $isCorrect = $test->segments[$s]->tasks[$t]->{$type}[$o]->correct == 1;
                            $isSelected = $test->segments[$s]->tasks[$t]->{$type}[$o]->selected == 1;
                            if ($isSelected) {
                                $option_points = $isCorrect ? $correctPoints : $wrongPoints;
                                $given_points += $option_points;
                                $test->segments[$s]->tasks[$t]->{$type}[$o]->given_points = $option_points / $precision;
                            }
                        }
                        $given_points = $given_points / $precision;
                        break;
                    case TaskType::CORRESPONDENCE:
                        \Log::info($test->segments[$s]->tasks[$t]);
                        //todo be able to calculate grades for answers and correct sides
                        break;
                    default:
                        $calculative = false;
                        break;
                }
                //Making sure no negative grading will be applied to the task
                if ($calculative) {
                    $test->segments[$s]->tasks[$t]->given_points = $given_points < 0 ? 0 : $given_points;
                }
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

    public function mergeUserAnswersToTest($test) {
        $user = $test->getUser($this->forUserId);
        if (is_null($user)) {
            return $test;
        }

        $field = 'answers';
        if (Auth::user()->role === UserRole::STUDENT) {
            $userData = $this->toArrayCurrentUser($user);
            if ($userData['has_draft']) {
                $field = 'answers_draft';
            }
        }

        $answers = $user->pivot->{$field};
        if ($answers) {
            for ($s = 0; $s < count($test->segments); $s++) {
                for ($t = 0; $t < count($test->segments[$s]->tasks); $t++) {
                    foreach ($answers as $answer) {
                        if ($test->segments[$s]->tasks[$t]->id == $answer['id']) {
                            switch ($test->segments[$s]->tasks[$t]->type) {
                                case TaskType::CMC:
                                case TaskType::RMC:
                                    for ($c = 0; $c < count($test->segments[$s]->tasks[$t]->{$answer['type']}); $c++) {
                                        foreach ($answer['data'] as $answeredChoice) {
                                            if ($answeredChoice['id'] == $test->segments[$s]->tasks[$t]->{$answer['type']}[$c]->id) {
                                                $test->segments[$s]->tasks[$t]->{$answer['type']}[$c]->selected = $answeredChoice['correct'];
                                            }
                                        }
                                    }
                                    break;
                                case TaskType::FREE_TEXT:
                                case  TaskType::CORRESPONDENCE:
                                    if (array_key_exists('data', $answer)) {
                                        $test->segments[$s]->tasks[$t]->answer = $answer['data'];
                                    }
                                    break;
                                default:
                                    //code
                            }
                        }
                    }
                }
            }
        }
        return $test;
    }


    public function prepareForUser(Test $test) {
        if (Auth::user()->role === UserRole::STUDENT) {
            $test = $this->forUserId(Auth::id())->withUserAnswers()->mergeUserAnswersToTest($test);
        }
        return $this->toArray($test);
    }

    public function toArray(Test $test) {
        $initial = $test->toArray();
        $final = [
            'id'           => $test->id,
            'name'         => $test->name,
            'description'  => $test->description,
            'status'       => $test->status,
            'can_register' => $test->can_register,
            'user_on_test' => $test->user_on_test,
            'duration'     => $test->duration,
            'lesson'       => $test->lesson->name,
            'segments'     => $this->toArraySegments($test->segments),
            'users'        => $this->toArrayUsers($test->users),
            'scheduled_at' => (!is_null($test->scheduled_at) ? $test->scheduled_at->format('d M, H:i') : '-'),
            'initial'      => $initial,
        ];

        $userOnTest = $test->user_on_test;
        if (Auth::user()->role == UserRole::STUDENT && !is_null($userOnTest)) {
            $final['current_user'] = $this->toArrayCurrentUser($userOnTest);
        }
        return $final;
    }

    private function toArrayUsers($users) {
        $data = [];
        foreach ($users as $u) {
            $data[] = [
                'id'     => $u->id,
                'name'   => $u->name,
                'role'   => $u->role,
                'status' => $u->pivot->status,
            ];
        }
        return $data;
    }

    private function toArrayCurrentUser($userOnTest) {
        $hasDraft = false;
        $lastSaved = null;
        if (!is_null($userOnTest->pivot->answered_draft_at)) {
            $lastSaved = $userOnTest->pivot->answered_draft_at;
            if (is_null($userOnTest->pivot->answered_at) || Carbon::parse($userOnTest->pivot->answered_at)->lt($userOnTest->pivot->answered_draft_at)) {
                $hasDraft = true;
            } else {
                $lastSaved = $userOnTest->pivot->answered_at;
            }
        }
        return [
            'status'     => $userOnTest->pivot->status,
            'has_draft'  => $hasDraft,
            'last_saved' => $lastSaved,
        ];
    }

    public function toArraySegments($segments) {
        $data = [];
        foreach ($segments as $s) {
            $data[] = $this->toArraySegment($s);
        }
        return $data;
    }

    public function toArraySegment($s) {
            $segment = [
                'id'          => $s->id,
                'title'       => $s->title,
                'description' => $s->description,
                'tasks'       => [],
            ];
            foreach ($s->tasks as $t) {
                $task = [
                    'id'          => $t->id,
                    'type'        => $t->type,
                    'position'    => $t->position,
                    'description' => $t->description,
                    'points'      => $t->points,
                ];
                $calculative = true;
                $given_points = 0;
                switch ($t->type) {
                    case TaskType::CMC:
                    case TaskType::RMC:
                        $counts = [
                            'total'   => 0,
                            'correct' => 0,
                            'wrong'   => 0,
                        ];
                        $choices = [];
                        foreach ($t->{$t->type} as $choice) {
                            $choiceData = [
                                'id'          => $choice->id,
                                'description' => $choice->description,
                            ];
                            if ($this->includeUserAnswers) {
                                $choiceData['selected'] = $choice->selected;
                            }
                            if ($this->includeCorrectAnswers) {
                                $choiceData['correct'] = $choice->correct;
                            }

                            $choices[] = $choiceData;

                            $counts['total']++;
                            if ($choice->correct) {
                                $counts['correct']++;
                            }
                        }
                        $counts['wrong'] = $counts['total'] - $counts['correct'];
                        $task['choices'] = $choices;
                        break;
                    case TaskType::CORRESPONDENCE:
                        $choices = [];
                        $pairs = [];
                        foreach ($t->{$t->type} as $choice) {
                            $pairs[$choice->side_a] = $choice->side_b;
                        }
                        $answers = [];
                        if (isset($t->answers)) {
                            foreach ($t->answers as $choice) {
                                $answers[$choice['side_a']] = $choice['side_b'];
                            }
                        }

                        $sideA = array_keys($pairs);
                        $sideB = array_values($pairs);
                        shuffle($sideB);
                        shuffle($sideA);

                        foreach ($sideA as $a) {
                            $payload = ['available' => $sideB];
                            if ($this->includeUserAnswers && array_key_exists($a, $answers)) {
                                $payload['selected'] = $answers[$a];
                            }
                            if ($this->includeCorrectAnswers) {
                                $payload['correct'] = $pairs[$a];
                            }
                            $choices[$a] = $payload;
                        }

                        $task['choices'] = $choices;
                        break;
                    case TaskType::FREE_TEXT:
                        if ($this->includeUserAnswers) {
                            $task['answer'] = $t->answer;
                        }
                        $calculative = false;
                        break;
                    default:
                }

                if ($calculative && $this->includeUserCalculatedPoints) {
                    switch ($t->type) {
                        case TaskType::RMC:
                            //FULL points are given if correct option is selected
                            //0 points are given if any wrong option is selected
                            for ($o = 0; $o < count($task['choices']); $o++) {
                                $isCorrect = $task['choices'][$o]['correct'] == 1;
                                $isSelected = $task['choices'][$o]['selected'] == 1;
                                if ($isCorrect && $isSelected) {
                                    $given_points = $task['points'];
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
                            $precision = 100;

                            //making sure we wont divide something with 0 by accident and we always have integers to add or subtract
                            $correctPoints = ($counts['correct'] == 0 ? 0 : round(+$precision * ($task['points'] / $counts['correct']))); //Positive multiplied with precision and rounded
                            $wrongPoints = ($counts['wrong'] == 0 ? 0 : round(-$precision * ($task['points'] / $counts['wrong'])));   //Negative in order to subtract from positive points

                            for ($o = 0; $o < count($task['choices']); $o++) {
                                $isCorrect = $task['choices'][$o]['correct'] == 1;
                                $isSelected = $task['choices'][$o]['selected'] == 1;
                                if ($isSelected) {
                                    $option_points = $isCorrect ? $correctPoints : $wrongPoints;
                                    $given_points += $option_points;
                                    $task['choices'][$o]['given_points'] = $option_points / $precision;
                                }
                            }
                            $given_points = $given_points / $precision;
                            break;
                    }

                    //Making sure no negative grading will be applied to the task
                    $task['given_points'] = $given_points < 0 ? 0 : $given_points;
                }
                $segment['tasks'][] = $task;
            }
            return $segment;
    }
}
