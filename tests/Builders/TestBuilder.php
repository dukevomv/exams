<?php

namespace Tests\Builders;

use App\Enums\TaskType;
use App\Enums\TestStatus;
use App\Enums\TestUserStatus;
use App\Models\Test;
use App\Services\TestServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Tests\Builders\Traits\AddsLessonId;

/**
 * Class TestBuilder
 *
 * @package Tests\Builders
 */
class TestBuilder extends ModelBuilder {

    private $service;
    private $segments = [];
    private $users    = [];

    use AddsLessonId;

    public function __construct() {
        $this->service = app()->make(TestServiceInterface::class);
    }


    /**
     * @param null $date
     *
     * @return $this
     */
    public function published($date = null) {
        $this->attributes['status'] = TestStatus::PUBLISHED;
        $this->attributes['scheduled_at'] = !is_null($date) ? $date : Carbon::now();
        return $this;
    }

    /**
     * @return $this
     */
    public function draft() {
        $this->attributes['status'] = TestStatus::DRAFT;
        return $this;
    }

    /**
     * @param null $date
     *
     * @return $this
     */
    public function started($date = null) {
        $this->attributes['status'] = TestStatus::STARTED;
        $this->attributes['scheduled_at'] = !is_null($date) ? $date : Carbon::now()->subMinute();
        $this->attributes['started_at'] = !is_null($date) ? $date : Carbon::now();
        return $this;
    }

    /**
     * @param null $date
     *
     * @return $this
     */
    public function finished($date = null) {
        $this->attributes['status'] = TestStatus::FINISHED;
        $this->attributes['scheduled_at'] = !is_null($date) ? $date : Carbon::now()->subMinutes(2);
        $this->attributes['started_at'] = !is_null($date) ? $date : Carbon::now()->subMinute();
        $this->attributes['finished_at'] = !is_null($date) ? $date : Carbon::now();
        return $this;
    }

    /**
     * @param null $date
     *
     * @return $this
     */
    public function graded($date = null) {
        $this->attributes['status'] = TestStatus::GRADED;
        $this->attributes['scheduled_at'] = !is_null($date) ? $date : Carbon::now()->subMinutes(20);
        $this->attributes['started_at'] = !is_null($date) ? $date : Carbon::now()->subMinutes(10);
        $this->attributes['finished_at'] = !is_null($date) ? $date : Carbon::now()->subMinutes(2);
        $this->attributes['graded_at'] = !is_null($date) ? $date : Carbon::now();
        return $this;
    }

    /**
     * Adds segment with tasks on test creation
     * Receives array of tasks containing their details in associative array.
     *
     * eg. [
     *       [
     *           'type'         => TaskType::CMC,
     *           'points'       => 5,
     *           'description'  => 'only animals',
     *           'options'      => [                //Options can also be an integer or associative array
     *               'car'      => false,
     *               'dog'      => true,
     *               'cat'      => true,
     *               'listen'   => false,
     *               'foot'     => false,
     *           ],
     *           'answers'      => [
     *             $studentId => [                  //Will add user if not exists and submits answers below
     *               'car'      => true,
     *               'dog'      => true,
     *               'cat'      => true,
     *               'listen'   => true,
     *               'foot'     => true,
     *             ],
     *           ],
     *       ],
     *   ]
     *
     * @param array $tasks
     *
     * @return $this
     */
    public function withSegmentTasks($tasks = []) {
        $this->segments[] = $tasks;
        return $this;
    }

    /**
     * Adds user on test (pivot).
     *
     * @param $userId
     * @param array $pivot Pivot data, Defaults to ['status'=>'registered']
     *
     * @return $this
     */
    public function withUser($userId, $pivot = null) {
        //todo add here the basic logic  of statuses (left|participated|graded)
        if ($pivot == null) {
            $pivot = [
                'created_at' => Carbon::now(),
                'status'     => TestUserStatus::REGISTERED,
            ];
        } elseif (array_key_exists('answers', $pivot)) {
            $pivot['created_at'] = Carbon::now()->subMinutes(10);
            $pivot['answered_at'] = Carbon::now();
            $pivot['status'] = TestUserStatus::PARTICIPATED;
        } elseif (array_key_exists('published_grades', $pivot)) {
            $pivot['created_at'] = Carbon::now()->subMinutes(20);
            $pivot['answered_at'] = Carbon::now()->subMinutes(10);
            $pivot['answered_at'] = Carbon::now();
            $pivot['status'] = TestUserStatus::GRADED;
        }
        $this->users[$userId] = $pivot;
        return $this;
    }

    public function withUserLeft($userId) {
        return $this->withUser($userId, [
            'created_at' => Carbon::now()->subMinutes(10),
            'left_at'    => Carbon::now(),
            'status'     => TestUserStatus::LEFT,
        ]);
    }

    /**
     * @return Test
     */
    public function build() {
        $attrs = array_merge([], $this->attributes);

        $test = factory(Test::class)->create($attrs);
        $this->buildSegments($test);
        $this->service->setTest($test);
        $test = $this->service->updatePublishedData();

        $segments = $this->service->prepareForPublish();

        foreach ($this->users as $userId => $pivot) {
            if(!in_array($pivot['status'],[TestUserStatus::REGISTERED,TestUserStatus::LEFT]) && !isset($pivot['answers'])) {
                $pivot['answers'] = json_encode($this->generateRandomStudentAnswersFromSegments($segments));
            }
            $test->users()->attach($userId, $pivot);
        }

        if($test->status == TestStatus::GRADED){
            $this->service->autoGradeUsers();
        }

        return $test;
    }

    private function generateRandomStudentAnswersFromSegments($segments){
        $answers = [];
        foreach($segments as $segment){
            foreach($segment['tasks'] as $task){
                $taskData = [
                    'id' => $task['id'],
                    'type' => $task['type'],
                ];
                switch ($task['type']) {
                    case TaskType::CMC:
                    case TaskType::RMC:
                        $taskData['data'] = [];
                        foreach($task['choices'] as $choice){
                            $taskData['data'][] = [
                                'id' => $choice['id'],
                                'correct' => rand(0,1)
                            ];
                        }
                        break;
                    case TaskType::CORRESPONDENCE:
                        $taskData['data'] = [];
                        $initializedChoices = false;
                        $unusedChoices = [];
                        foreach($task['choices'] as $a => $b){
                            if(!$initializedChoices){
                                $initializedChoices = true;
                                $unusedChoices = $b['available'];
                            }
                            $choiceIndex = array_rand($unusedChoices,1);
                            $taskData['data'][] = [
                                'side_a' => $a,
                                'side_b' => $unusedChoices[$choiceIndex]
                            ];
                            unset($unusedChoices[$choiceIndex]);
                        }
                        break;
                    case TaskType::FREE_TEXT:
                        $taskData['data'] = $this->faker->sentence(10);
                }
                $answers[] = $taskData;
            }
            return $answers;
        }
    }

    private function buildSegments(Test $test) {
        $ordered_segments = [];
        $position = 1;
        foreach ($this->segments as $seg) {
            $hasAnswers = false;
            $builder = SegmentBuilder::instance()->inLesson($test->lesson_id);
            foreach ($seg as $task) {
                $builder->withTask($task['type'], $task);
                if (!$hasAnswers && Arr::has($task, 'answers')) {
                    $hasAnswers = true;
                }
            }
            $position++;
            $segment = $builder->build();
            $ordered_segments[$segment->id] = ['position' => $position];
            if ($hasAnswers) {
                //Get Segment tasks with populated Ids by SegmentBuilder
                $segmentTasks = $builder->getTasks();
                foreach ($segmentTasks as $task) {
                    if (Arr::has($task, 'answers')) {
                        $TaskAnswerData = ['id' => $task['id'], 'type' => $task['type']];
                        foreach ($task['answers'] as $studentId => $answer) {
                            switch ($task['type']) {
                                case TaskType::CMC:
                                case TaskType::RMC:
                                    $TaskAnswerData['data'] = [];
                                    foreach ($task['options'] as $profOption) {
                                        foreach ($answer as $studentOptionKey => $studentOptionValue) {
                                            //todo be able to parse associative, only correct answers, different answers payload
                                            if ($studentOptionKey == $profOption['description']) {
                                                //when key is the same with option's description
                                                $TaskAnswerData['data'][] = [
                                                    'id'      => $profOption['id'],
                                                    'correct' => $studentOptionValue,
                                                ];
                                            }
                                        }
                                    }
                                    break;
                                case TaskType::CORRESPONDENCE:
                                case TaskType::FREE_TEXT:
                                    //todo implement answers for all types
                                default:
                            }

                            if (!isset($this->users[$studentId])) {
                                $this->withUser($studentId);
                            }
                            if (!isset($this->users[$studentId]['task_answers'])) {
                                $this->users[$studentId]['task_answers'] = [];
                            }
                            $this->users[$studentId]['task_answers'][] = $TaskAnswerData;
                        }
                    }
                }
            }
        }
        $test->segments()->sync($ordered_segments);
        foreach ($this->users as $uid => $payload) {
            if (Arr::has($payload, 'task_answers')) {
                $this->users[$uid]['answers'] = json_encode($payload['task_answers']);
                unset($this->users[$uid]['task_answers']);
            }
        }
        return $test;
    }
}