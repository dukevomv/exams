<?php

namespace Tests\Builders;

use App\Enums\TaskType;
use App\Models\Segments\AnswerCmc;
use App\Models\Segments\AnswerRmc;
use App\Models\Segments\Segment;
use App\Models\Segments\Task;
use Illuminate\Support\Arr;
use Tests\Builders\Traits\AddsLessonId;

class SegmentBuilder extends ModelBuilder {

    use AddsLessonId;

    private $tasks = [];

    /**
     * @param null $type
     * @param array $values
     *
     * @return $this
     */
    public function withTask($type = null, $values = []) {
        $typeOptions = ['type' => is_null($type) ? $this->faker->randomElement(TaskType::values()) : $type];
        $this->tasks[] = array_merge($values, $typeOptions);
        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function withCMCTask($values = []) {
        $this->tasks[] = array_merge($values, ['type' => TaskType::CMC]);
        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function withRMCTask($values = []) {
        $this->tasks[] = array_merge($values, ['type' => TaskType::RMC]);
        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function withFreeTextTask($values = []) {
        $this->tasks[] = array_merge($values, ['type' => TaskType::FREE_TEXT]);
        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function withCorrespondenceTask($values = []) {
        $this->tasks[] = array_merge($values, ['type' => TaskType::CORRESPONDENCE]);
        return $this;
    }

    /**
     * @return Segment
     */
    public function build() {
        $attrs = array_merge([], $this->attributes);
        $segment = factory(Segment::class)->create($attrs);
        $this->buildTasks($segment);
        return $segment->load('tasks');
    }

    private function buildTasks(Segment $segment) {
        $position = 0;
        foreach ($this->tasks as $taskData) {
            $type = $taskData['type'];
            $task = factory(Task::class)
                ->states([$type])
                ->create(
                    array_merge(
                        Arr::only($taskData, ['description', 'points']),
                        [
                            'segment_id' => $segment->id,
                            'position'   => $position,
                        ]
                    ));
            $position++;

            switch ($type) {
                case TaskType::CMC:
                case TaskType::RMC:
                    $answerClass = [
                        TaskType::CMC => AnswerCmc::class,
                        TaskType::RMC => AnswerRmc::class,
                    ];
                    $commons = ['task_id' => $task->id];
                    if (!Arr::has($taskData, 'options')) {
                        //if options is a number, create that many options
                        factory($answerClass[$type], $this->faker->numberBetween(1, 10))->create($commons);
                    } elseif (is_integer($taskData['options'])) {
                        //if options is a number, create that many options
                        factory($answerClass[$type], $taskData['options'])->create($commons);
                    } elseif (count($taskData['options']) > 0) {
                        //if options is an array
                        foreach ($taskData['options'] as $optionKey => $option) {
                            if (is_integer($optionKey) && (isset($option['description']) || isset($option['correct']))) {
                                //$optionKey is array with associative array in $option
                                factory($answerClass[$type])->create(array_merge($commons, $option));
                            } elseif (is_bool($option)) {
                                //$optionKey is the option description with correct boolean in $option
                                factory($answerClass[$type])->create(array_merge($commons, [
                                    'description' => $optionKey,
                                    'correct'     => $option,
                                ]));
                            }
                        }
                    }
                    break;
                default:
            }
        }
    }
}