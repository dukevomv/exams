<?php

namespace App\Services;

use App\Enums\TaskType;
use App\Enums\TestStatus;
use App\Enums\TestUserStatus;
use App\Enums\UserRole;
use App\Models\Lesson;
use App\Models\Segments\Segment;
use App\Models\Test;
use App\Models\User;
use App\Util\Points;
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
    private $userAnswers                 = [];
    private $grades                      = [];

    public function forUserId($userId) {
        $this->forUserId = $userId;
        return $this;
    }

    public function setTest(Test $test) {
        $this->test = $test;
        return $this;
    }

    public function withCorrectAnswers() {
        $this->includeCorrectAnswers = true;
        return $this;
    }

    public function withoutCorrectAnswers() {
        $this->includeCorrectAnswers = false;
        return $this;
    }

    public function withUserAnswers() {
        $this->includeUserAnswers = true;
        $this->userAnswers = $this->getUserAnswers();
        return $this;
    }

    public function withoutUserAnswers() {
        $this->includeUserAnswers = false;
        $this->userAnswers = [];
        return $this;
    }

    public function withUserCalculatedPoints() {
        $this->includeUserCalculatedPoints = true;
        $this->grades = $this->getUserGrades();
        return $this;
    }

    public function withoutUserCalculatedPoints() {
        $this->includeUserCalculatedPoints = false;
        $this->grades = [];
        return $this;
    }

    public function get(array $params = []) {
        $tests = Test::withCount('segments')->whereIn('lesson_id', self::getApprovedLessonIds());

        if (!is_null(Arr::get($params, 'lesson', null))) {
            $tests->where('lesson_id', Arr::get($params, 'lesson'));
        }

        if (!is_null(Arr::get($params, 'search', null))) {
            $tests->search(Arr::get($params, 'search'));
        }

        if (!is_null(Arr::get($params, 'status', null))) {
            $tests->where('status', Arr::get($params, 'status'));
        }

        switch (Auth::user()->role) {
            case UserRole::STUDENT:
                $tests->where('status', '!=', TestStatus::DRAFT);
                break;
            default:
                break;
        }

        $results = is_null(Arr::get($params, 'paginate', null)) ? $tests->get() : $tests->paginate(10);

        if (Auth::user()->role == UserRole::PROFESSOR) {
            for ($i = 0; $i < count($results); $i++) {
                if (in_array($results[$i]->status, [TestStatus::GRADED, TestStatus::FINISHED])) {
                    $this->setTest($results[$i]);
                    $results[$i]->stats = self::generateResults($this->toArrayUsers(), $results[$i]->getPublishedSegmentData());
                }
            }
        }
        return $results;
    }

    public function fetchById($id, $firstOrFail = true) {
        $query = Test::with('segments.tasks', 'users', 'user')
                     ->where('id', $id)
                     ->whereIn('lesson_id', self::getApprovedLessonIds())
                     ->withSegmentTaskAnswers();

        return $firstOrFail ? $query->firstOrFail() : $query->first();
    }

    public function setById($id) {
        $test = $this->fetchById($id);
        $this->setTest($test);
        return $test;
    }

    public function updateOrCreate($id, $fields, $segments) {
        $existing = $this->fetchById($id, false);
        if (!is_null($existing) && !in_array($existing->status, [TestStatus::PUBLISHED, TestStatus::DRAFT])) {
            abort(400, 'You cannot update this test');
        }
        $test = Test::updateOrCreate(['id' => $id], $fields);
        $this->setTest($test);

        $ordered_segments = [];
        $count = 1;
        foreach ($segments as $req_segment) {
            $ordered_segments[$req_segment] = ['position' => $count];
            $count++;
        }

        $test->segments()->sync($ordered_segments);
        $test = $this->updatePublishedData();
        return $test;
    }

    public function updatePublishedData() {
        switch ($this->test->status) {
            case TestStatus::DRAFT:
                $this->test->unpublishSegmentData();
                break;
            case TestStatus::PUBLISHED:
            case TestStatus::STARTED:
            case TestStatus::FINISHED:
            case TestStatus::GRADED:
                $this->test->publishSegmentData($this->prepareForPublish());
                break;
            default:
        }
        return $this->test;
    }

    public function calculateUserPoints($userId) {
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

    private static function getApprovedLessonIds() {
        return Lesson::approved()->get()->pluck('id')->all();
    }

    private function getUserAnswers() {
        $user = $this->test->getUser($this->forUserId);
        if (is_null($user)) {
            return [];
        }
        $field = 'answers';
        if ($this->forUserId == Auth::id() && Auth::user()->role === UserRole::STUDENT) {
            $userData = $this->toArrayCurrentUser($user);
            if ($userData['has_draft']) {
                $field = 'answers_draft';
            }
        }
        $data = json_decode($user->pivot->{$field}, true);
        return is_null($data) ? [] : $data;
    }

    //todo :deprecated
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

    public function autoGradeUsers() {
        if (!self::isTestAutoCalculative($this->toArraySegments())) {
            abort(400, 'Test contains tasks that can not be auto graded.');
        }
        $users = $this->test->users()->get()->all();
        foreach ($users as $user) {
            if (!in_array($user->pivot->status, [TestUserStatus::LEFT, TestUserStatus::REGISTERED])) {
                $this->autoGradeForUser($user->id, true);
            }
        }
        return [];
    }

    public function publishTestGrades() {
        if (!$this->isTestGradesArePublishable()) {
            abort(400, 'There are participated students that are not yet graded.');
        }
        $this->test->grade();
        return [];
    }

    private function isTestGradesArePublishable() {
        $publishable = true;
        $users = $this->test->users()->get()->all();
        foreach ($users as $user) {
            if ($user->pivot->status == TestUserStatus::PARTICIPATED) {
                $publishable = false;
                break;
            }
        }
        return $publishable;
    }

    public function autoGradeForUser($userId, $publish = false) {
        $existingGrades = $this->getUserGrades($userId);
        $this->calculateUserPoints($userId);
        $testData = $this->prepareForUser($userId);
        if (!self::userIsGradable($this->test->getUser($userId))) {
            return [];
        }
        $existingGrades = self::calculateGrades($existingGrades, $testData);
        $this->saveUserGrades($existingGrades);
        if ($publish) {
            $this->test->publishProfessorGrade($userId);
        }
    }

    private static function calculateGrades($existingGrades, $testContents) {
        foreach ($testContents['segments'] as $segment) {
            foreach ($segment['tasks'] as $task) {
                if ($task['calculative'] && !$task['manually_saved']) {
                    $existingGrades[self::getGradeTaskKey($task['id'])] = $task['given_points'];
                }
            }
        }
        return $existingGrades;
    }


    /**
     * @param \App\Models\Test $test
     * @param $payload
     *
     * @return array
     */
    public function gradeUserTask($payload) {
        if (!self::userIsGradable($this->test->getUser($this->forUserId))) {
            return [];
        }
        $existingGrades = $this->getUserGrades();
        $gradeExisted = false;
        foreach ($existingGrades as $taskId => $grade) {
            if ($taskId === self::getGradeTaskKey($payload['task_id'])) {
                $existingGrades[$taskId] = $payload['points'];
                $gradeExisted = true;
            }
        }
        if (!$gradeExisted) {
            $existingGrades[self::getGradeTaskKey($payload['task_id'])] = $payload['points'];
        }
        $this->saveUserGrades($existingGrades);
        return [];
    }

    private static function userIsGradable(User $user) {
        return !in_array($user->pivot->status, [TestUserStatus::REGISTERED, TestUserStatus::LEFT]);
    }

    private function saveUserGrades(array $grades) {
        $given = array_sum($grades);
        $total = array_sum(Arr::pluck($this->toArraySegments(), 'total_points'));
        $this->test->saveProfessorGrade($this->forUserId, $grades, $given, $total);
    }

    private function getUserGrades($userId = null) {
        if (is_null($userId)) {
            $userId = $this->forUserId;
        }
        $user = $this->test->getUser($userId);
        return (is_null($user) || is_null($user->pivot->grades)) ? [] : json_decode($user->pivot->grades, true);
    }

    private static function getTaskGradeFromUserGrades($existingGrades, $taskId) {
        $taskGrade = null;
        foreach ($existingGrades as $taskKey => $grade) {
            if ($taskKey === self::getGradeTaskKey($taskId)) {
                $taskGrade = $existingGrades[$taskKey];
            }
        }
        return $taskGrade;
    }

    private static function getGradeTaskKey($taskId) {
        return 'task_id_' . $taskId;
    }

    private static function stripGradeTaskKey($taskId) {
        return str_replace('task_id_', '', $taskId);
    }

    public function prepareForPublish() {
        $this->withoutUserAnswers()
             ->withoutUserCalculatedPoints()
             ->withCorrectAnswers();
        return $this->toArraySegments();
    }

    public function prepareForCurrentUser() {
        if (Auth::user()->role === UserRole::STUDENT) {
            $this->prepareForUser(Auth::id());
        }
        return $this->toArray();
    }

    public function prepareForUser($userId) {
        return $this->forUserId($userId)->withUserAnswers()->toArray();
    }

    public function toArray() {
        $initial = $this->test->toArray();
        $final = [
            'id'                 => $this->test->id,
            'name'               => $this->test->name,
            'description'        => $this->test->description,
            'status'             => $this->test->status,
            'can_register'       => $this->test->can_register,
            'register_time'      => $this->test->register_time,
            'duration'           => $this->test->duration,
            'lesson'             => $this->test->lesson->name,
            'segments'           => $this->toArraySegments(),
            'users'              => $this->toArrayUsers(),
            'scheduled_at'       => (!is_null($this->test->scheduled_at) ? $this->test->scheduled_at->format('d M, H:i') : '-'),
            'initial'            => $initial,
            'with_grades'        => $this->includeUserCalculatedPoints,
            'grades_publishable' => $this->isTestGradesArePublishable(),
        ];

        if (Auth::user()) {
            if (Auth::user()->role == UserRole::PROFESSOR) {
                $final['auto_calculative'] = self::isTestAutoCalculative($final['segments']);
                if (in_array($this->test->status, [TestStatus::FINISHED, TestStatus::GRADED])) {
                    $final['stats'] = self::generateResults($final['users'], $final['segments']);
                }
                if (!is_null($this->forUserId)) {
                    $final['for_student'] = $this->toArrayStudent($final['segments']);
                }
            }

            $userOnTest = $this->test->user_on_test;
            if (Auth::user()->role == UserRole::STUDENT && !is_null($userOnTest)) {
                $final['current_user'] = $this->toArrayCurrentUser($userOnTest);
            }
        }
        return $final;
    }

    private static function calculateTotalPoints($segments) {
        return collect($segments)->sum('total_points');
    }

    private static function generateResults($users, $segments) {
        if (count($users) == 0) {
            return null;
        }
        $total = self::calculateTotalPoints($segments);
        $results = [
            'max'                => 0,
            'min'                => $total,
            'range'              => 0,
            'standard_deviation' => 0,
            'test_max_points'    => $total,
            'sum_total_points'   => 0,
            'sum_given_points'   => 0,
            'students'           => [
                'graded'       => 0,
                'participated' => 0,
                'passed'       => 0,
                'dodged'       => 0,
                'total'        => 0,
            ],
        ];
        $results['baseline'] = $total / 2;
        $gradesArr = [];
        foreach ($users as $user) {
            $results['students']['total']++;
            if (in_array($user['status'], [TestUserStatus::REGISTERED, TestUserStatus::LEFT])) {
                $results['students']['dodged']++;
            } else {
                $results['students']['participated']++;
                if ($user['status'] === TestUserStatus::GRADED) {
                    $results['students']['graded']++;
                    $gradesArr[] = $user['given_points'];
                    $results['sum_total_points'] += $user['total_points'];
                    $results['sum_given_points'] += $user['given_points'];
                    if ($results['max'] < $user['given_points']) {
                        $results['max'] = $user['given_points'];
                    }
                    if ($results['min'] > $user['given_points']) {
                        $results['min'] = $user['given_points'];
                    }
                    if ($results['baseline'] < $user['given_points']) {
                        $results['students']['passed']++;
                    }
                }
            }
        }
        if ($results['students']['participated'] == 0 || $total == 0 || ($results['students']['graded'] == 0)) {
            return null;
        }
        $results['average'] = Points::getWithPercentage(round($results['sum_given_points'] / $results['students']['participated'], 2), $total);
        $results['range'] = Points::getWithPercentage($results['max'] - $results['min'], $total);
        $results['min'] = Points::getWithPercentage($results['min'], $total);
        $results['max'] = Points::getWithPercentage($results['max'], $total);
        $results['standard_deviation'] = Points::getWithPercentage(Points::calcStandardDeviation($gradesArr), $total);
        return $results;
    }

    private static function isTestAutoCalculative($segments) {
        $itIs = true;
        foreach ($segments as $segment) {
            if (!$segment['auto_calculative']) {
                $itIs = false;
            }
        }
        return $itIs;
    }

    public function autoCalculateTestGradesForStudent($student) {
        return $this->test->publishProfessorGrade($student->id);
    }

    public function toArrayUsers() {
        $data = [];
        foreach ($this->test->users as $u) {
            $data[] = self::toArrayUser($u);
        }
        return $data;
    }

    private static function toArrayUser(User $u) {
        return [
            'id'           => $u->id,
            'name'         => $u->name,
            'role'         => $u->role,
            'status'       => $u->pivot->status,
            'entered_at'   => Carbon::parse($u->pivot->created_at)->diffForHumans(),
            'given_points' => $u->pivot->given_points,
            'total_points' => $u->pivot->total_points,
            'gradable'     => self::userIsGradable($u),
        ];
    }

    private function gradesArePublishable($student, $segments) {
        $gradedTaskCount = count($this->grades);
        $totalTasks = 0;
        foreach ($segments as $segment) {
            $totalTasks += count($segment['tasks']);
        }
        return $student->pivot->status !== TestUserStatus::GRADED && $gradedTaskCount == $totalTasks;
    }

    private function toArrayStudent(array $segmentsArray) {
        $student = $this->test->getUser($this->forUserId);
        if (is_null($student)) {
            return null;
        }
        $main = $this->toArrayUser($student);
        $total = 0;
        foreach ($segmentsArray as $segm) {
            $total += $segm['total_points'];
        }

        $main['publishable'] = $this->gradesArePublishable($student, $segmentsArray);

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

    public function toArraySegments() {
        $isPublished = self::isPublished($this->test);
        $segments = $isPublished ? $this->test->getPublishedSegmentData() : $this->test->segments;

        $data = [];
        foreach ($segments as $s) {
            $data[] = $this->toArraySegment($s, $isPublished);
        }
        return $data;
    }

    private static function isPublished(Test $test) {
        return $test->status !== TestStatus::DRAFT && $test->hasPublishedSegmentData();
    }

    public function toArraySegment($s, $fromPublished) {
        return $fromPublished ? $this->toArrayDBSegment($s) : $this->toArrayEloquentSegment($s);
    }

    public function toArrayTaskImages($images) {
        $final = [];
        foreach($images as $i){
            $final[] = [
              'id' => $i->id,
              'title' => $i->title,
              'url' => $i->url,
            ];
        }
        return $final;
    }

    //stores basic info and correct answers to db
    public function toArrayEloquentSegment($s) {
        $segment = [
            'id'           => $s->id,
            'title'        => $s->title,
            'description'  => $s->description,
            'tasks'        => [],
            'total_points' => 0,
        ];
        $isAutoCalculative = true;
        foreach ($s->tasks as $t) {
            $task = [
                'id'          => $t->id,
                'type'        => $t->type,
                'images'      => $this->toArrayTaskImages($t->images),
                'position'    => $t->position,
                'description' => $t->description,
                'points'      => $t->points,
                'calculative' => true,
            ];

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
                        $choices[] = [
                            'id'          => $choice->id,
                            'description' => $choice->description,
                            'correct'     => $choice->correct,
                        ];

                        $counts['total']++;
                        if ($choice->correct) {
                            $counts['correct']++;
                        }
                    }
                    $counts['wrong'] = $counts['total'] - $counts['correct'];
                    $task['choices'] = $choices;
                    $task['counts'] = $counts;
                    break;
                case TaskType::CORRESPONDENCE:
                    $choices = [];
                    $pairs = [];
                    foreach ($t->{$t->type} as $choice) {
                        $pairs[$choice->side_a] = $choice->side_b;
                    }
                    $sideA = array_keys($pairs);
                    $sideB = array_values($pairs);
                    shuffle($sideB);
                    shuffle($sideA);

                    foreach ($sideA as $a) {
                        $choices[$a] = [
                            'available' => $sideB,
                            'correct'   => $pairs[$a],
                        ];;
                    }
                    $task['choices'] = $choices;
                    break;
                case TaskType::FREE_TEXT:
                    $task['calculative'] = false;
                    $task['answer'] = isset($t->answer) ? $t->answer : '';
                    break;
                default:
            }
            $segment['total_points'] += $task['points'];
            if (!$task['calculative']) {
                $isAutoCalculative = false;
            }
            $segment['tasks'][] = $task;
        }
        $segment['auto_calculative'] = $isAutoCalculative;
        return $segment;
    }

    public function toArrayDBSegment($segment) {
        for ($t = 0; $t < count($segment['tasks']); $t++) {
            switch ($segment['tasks'][$t]['type']) {
                case TaskType::CMC:
                case TaskType::RMC:
                case TaskType::CORRESPONDENCE:
                    foreach ($segment['tasks'][$t]['choices'] as $key => $choice) {
                        if (!$this->includeCorrectAnswers) {
                            unset($segment['tasks'][$t]['choices'][$key]['correct']);
                        }
                    }
                    break;
            }

            if ($this->includeUserAnswers) {
                $segment['tasks'][$t] = $this->mergeUserAnswersToTask($segment['tasks'][$t]);
                if ($this->includeUserCalculatedPoints) {
                    $segment['tasks'][$t] = $this->mergeUserCalculatedPoints($segment['tasks'][$t]);
                    if (!array_key_exists('total_given_points', $segment)) {
                        $segment['total_given_points'] = 0;
                    }
                    $segment['total_given_points'] += $segment['tasks'][$t]['given_points'];
                    //total points are calculated from Eloquent object
                    //$segment['total_points'] += $segment['tasks'][$t]['points'];
                }
            }
        }
        $segment['changed'] = $this->isPublishedSegmentChanged($segment['id']);
        return $segment;
    }

    private function isPublishedSegmentChanged($segmentId) {
        $segment = Segment::find($segmentId);
        return Carbon::make($segment->updated_at)->gt($this->test->published_at);
    }

    private function mergeUserAnswersToTask($task) {
        foreach ($this->userAnswers as $answer) {
            if ($task['id'] == $answer['id']) {
                switch ($task['type']) {
                    case TaskType::CMC:
                    case TaskType::RMC:
                        for ($c = 0; $c < count($task['choices']); $c++) {
                            foreach ($answer['data'] as $answeredChoice) {
                                if ($answeredChoice['id'] == $task['choices'][$c]['id']) {
                                    $task['choices'][$c]['selected'] = $answeredChoice['correct'];
                                }
                            }
                        }
                        break;
                    case  TaskType::CORRESPONDENCE:
                        if (array_key_exists('data', $answer)) {
                            foreach ($answer['data'] as $choice) {
                                $task['choices'][$choice['side_a']]['selected'] = $choice['side_b'];
                            }
                        }
                        break;
                    case TaskType::FREE_TEXT:
                        if (array_key_exists('data', $answer)) {
                            $task['answer'] = $answer['data'];
                        }
                    default:
                        //code
                }
            }
        }
        return $task;
    }

    private function mergeUserCalculatedPoints($task) {
        $task['manually_saved'] = false;
        $taskGrade = self::getTaskGradeFromUserGrades($this->grades, $task['id']);
        $taskGradeExists = !is_null($taskGrade);

        $given_points = 0;
        if ($taskGradeExists) {
            $given_points = $taskGrade;
            $task['manually_saved'] = true;
        } elseif ($task['calculative']) {
            switch ($task['type']) {
                case TaskType::RMC:
                    //FULL points are given if correct option is selected
                    //0 points are given if any wrong option is selected
                    for ($o = 0; $o < count($task['choices']); $o++) {
                        $isCorrect = $task['choices'][$o]['correct'] == 1;
                        $isSelected = isset($task['choices'][$o]['selected']) && $task['choices'][$o]['selected'] == 1;
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
                    $correctPoints = ($task['counts']['correct'] == 0 ? 0 : round(+$precision * ($task['points'] / $task['counts']['correct']))); //Positive multiplied with precision and rounded
                    $wrongPoints = ($task['counts']['wrong'] == 0 ? 0 : round(-$precision * ($task['points'] / $task['counts']['wrong'])));   //Negative in order to subtract from positive points
                    for ($o = 0; $o < count($task['choices']); $o++) {
                        $isCorrect = $task['choices'][$o]['correct'] == 1;
                        $isSelected = isset($task['choices'][$o]['selected']) && $task['choices'][$o]['selected'] == 1;
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
                            $isCorrect = isset($task['choices'][$a]['selected']) && $task['choices'][$a]['selected'] == $task['choices'][$a]['correct'];
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
        return $task;
    }
}
