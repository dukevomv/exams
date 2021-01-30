<?php

namespace App\Services;

use App\Enums\TaskType;
use App\Enums\TestStatus;
use App\Enums\TestUserStatus;
use App\Enums\UserRole;
use App\Models\Lesson;
use App\Models\Segments\Task;
use App\Models\Test;
use App\Models\User;
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
        $this->forUserId($userId)
             ->withCorrectAnswers()
             ->withUserAnswers()
             ->withUserCalculatedPoints();
        return $this;
    }

    public function calculateTimer(Test $test) {
        $timer = [
            'running'                 => false,
            'in_delay'                => true,
            'remaining_seconds'       => $test->duration * 60,
            'start_delay_in_seconds'  => config('app.bm.test_timer.start_delay_in_seconds'),
            'finish_delay_in_seconds' => config('app.bm.test_timer.finish_delay_in_seconds'),
            'server_time'             => Carbon::now(),
        ];

        if (in_array($test->status, [TestStatus::STARTED, TestStatus::FINISHED])) {
            $timer['running'] = true;
            $status_changed_at = Carbon::parse($test->{$test->status . '_at'});
            if ($timer['server_time']->gte($status_changed_at)) {
                $timer['in_delay'] = false;
                if ($test->status === TestStatus::STARTED) {
                    $should_finish = $status_changed_at->copy()->addMinutes($test->duration);

                    if ($timer['server_time']->lte($should_finish)) {
                        $timer['remaining_seconds'] = $timer['server_time']->diffInSeconds($should_finish);
                    } else {
                        $timer['remaining_seconds'] = 0;
                        $timer['running'] = false;
                    }
                } elseif ($test->status === TestStatus::FINISHED) {
                    $timer['remaining_seconds'] = 0;
                    $timer['running'] = false;
                }
            } else {
                $timer['remaining_seconds'] = $timer['server_time']->diffInSeconds($status_changed_at);
                $timer['in_delay'] = true;
            }
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

        //todo make the below to be parsable with cast in pivot model
        $answers = json_decode($user->pivot->{$field}, true);
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


    /**
     * @param \App\Models\Test $test
     * @param $payload
     *
     * @return array
     */
    public function autoGradeUser(Test $test) {
        $existingGrades = $this->getUserGrades($test);
        $testData = $this->prepareForUser($test);
        foreach($testData['segments'] as $segment){
            foreach($segment['tasks'] as $task){
                if(!$task['manually_saved'] && $task['calculative']){
                    $gradedTaskIds[] = $task['id'];
                    $existingGrades[$this->getGradeTaskKey($task['id'])] = $task['given_points'];
                }
            }
        }
        $this->saveUserGrades($test, $existingGrades);
        //todo return value that is needed for ajax call
        return [];
    }

    /**
     * @param \App\Models\Test $test
     * @param $payload
     *
     * @return array
     */
    public function gradeUserTask(Test $test, $payload) {
        $existingGrades = $this->getUserGrades($test);
        $gradeExisted = false;
        foreach ($existingGrades as $taskId => $grade) {
            if ($taskId === $this->getGradeTaskKey($payload['task_id'])) {
                $existingGrades[$taskId] = $payload['points'];
                $gradeExisted = true;
            }
        }
        if (!$gradeExisted) {
            $existingGrades[$this->getGradeTaskKey($payload['task_id'])] = $payload['points'];
        }
        $this->saveUserGrades($test, $existingGrades);
        //todo return value that is needed for ajax call
        return [];
    }

    private function saveUserGrades(Test $test, array $grades) {
        $gradedTaskIds = [];
        $given = 0;
        foreach ($grades as $taskId => $grade) {
            $gradedTaskIds[] = $this->stripGradeTaskKey($taskId);
            $given += $grade;
        }
        $total = Task::whereIn('id', $gradedTaskIds)->sum('points');
        $test->saveProfessorGrade($this->forUserId, $grades, $given, $total);
    }

    private function getUserGrades(Test $test) {
        $user = $test->getUser($this->forUserId);
        return (is_null($user) || is_null($user->pivot->grades)) ? [] : json_decode($user->pivot->grades, true);
    }

    private function getTaskGradeFromUserGrades($existingGrades, $taskId) {
        $taskGrade = null;
        foreach ($existingGrades as $taskKey => $grade) {
            if ($taskKey === $this->getGradeTaskKey($taskId)) {
                $taskGrade = $existingGrades[$taskKey];
            }
        }
        return $taskGrade;
    }

    private function getGradeTaskKey($taskId) {
        return 'task_id_' . $taskId;
    }

    private function stripGradeTaskKey($taskId) {
        return str_replace('task_id_', '', $taskId);
    }

    public function prepareForUser(Test $test) {
        if (Auth::user()->role === UserRole::STUDENT) {
            $this->forUserId(Auth::id())->withUserAnswers();
        }
        $test = $this->mergeUserAnswersToTest($test);
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
            'duration'     => $test->duration,
            'lesson'       => $test->lesson->name,
            'segments'     => $this->toArraySegments($test),
            'users'        => $this->toArrayUsers($test->users),
            'scheduled_at' => (!is_null($test->scheduled_at) ? $test->scheduled_at->format('d M, H:i') : '-'),
            'initial'      => $initial,
            'with_grades'  => $this->includeUserCalculatedPoints,
        ];

        if (!is_null($this->forUserId)) {
            $final['for_student'] = $this->toArrayStudent($test, $final['segments']);
        }

        $userOnTest = $test->user_on_test;
        if (Auth::user()->role == UserRole::STUDENT && !is_null($userOnTest)) {
            $final['current_user'] = $this->toArrayCurrentUser($userOnTest);
        }
        return $final;
    }

    private function toArrayUsers($users) {
        $data = [];
        foreach ($users as $u) {
            \Log::info($u->pivot);
            $data[] = $this->toArrayUser($u);
        }
        return $data;
    }

    private function toArrayUser(User $u) {
        return [
            'id'           => $u->id,
            'name'         => $u->name,
            'role'         => $u->role,
            'status'       => $u->pivot->status,
            'entered_at'   => Carbon::parse($u->pivot->created_at)->diffForHumans(),
            'given_points' => $u->pivot->given_points,
            'total_points' => $u->pivot->total_points,
        ];
    }

    private function toArrayStudent(Test $test, array $segmentsArray) {
        $student = $test->getUser($this->forUserId);
        $main = $this->toArrayUser($student);
        $total = 0;
        foreach ($segmentsArray as $segm) {
            $total += $segm['total_points'];
        }

        $main['publishable'] = $student->pivot->status !== TestUserStatus::GRADED
            && $total == $student->pivot->total_points;

        return $main;
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

    public function toArraySegments(Test $test) {
        $segments = $test->segments;
        $grades = $this->getUserGrades($test);
        $data = [];
        foreach ($segments as $s) {
            $data[] = $this->toArraySegment($s, $grades);
        }
        return $data;
    }

    public function toArraySegment($s, $grades = []) {
        $segment = [
            'id'           => $s->id,
            'title'        => $s->title,
            'description'  => $s->description,
            'tasks'        => [],
            'total_points' => 0,
        ];
        foreach ($s->tasks as $t) {
            $task = [
                'id'             => $t->id,
                'type'           => $t->type,
                'position'       => $t->position,
                'description'    => $t->description,
                'points'         => $t->points,
                'manually_saved' => false,
                'calculative'    => true,
            ];

            $taskGrade = $this->getTaskGradeFromUserGrades($grades, $t->id);
            $taskGradeExists = !is_null($taskGrade);

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
                    if ($this->includeUserAnswers && isset($t->answer)) {
                        foreach ($t->answer as $choice) {
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
                    if ($this->includeUserAnswers && isset($t->answer)) {
                        $task['answer'] = $t->answer;
                    }
                    $task['calculative'] = false;
                    break;
                default:
            }
            if ($this->includeUserCalculatedPoints) {
                $given_points = 0;
                if ($taskGradeExists) {
                    $given_points = $taskGrade;
                    $task['manually_saved'] = true;
                } elseif ($task['calculative']) {
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
                        case TaskType::CORRESPONDENCE:
                            $total = count($task['choices']);
                            foreach ($task['choices'] as $a => $b) {
                                $isCorrect = false;
                                if (array_key_exists('selected', $task['choices'][$a])) {
                                    $isCorrect = $task['choices'][$a]['correct'] == $task['choices'][$a]['selected'];
                                }
                                if ($isCorrect) {
                                    $given_points += $task['points'] / $total;
                                }
                            }
                            break;
                    }
                }

                //Making sure no negative grading will be applied to the task
                $task['given_points'] = $given_points <= 0 ? 0 : round($given_points, 2);
                if (!array_key_exists('total_given_points', $segment)) {
                    $segment['total_given_points'] = 0;
                }
                $segment['total_given_points'] += $task['given_points'];
            }
            $segment['total_points'] += $task['points'];
            $segment['tasks'][] = $task;
        }
        return $segment;
    }
}
