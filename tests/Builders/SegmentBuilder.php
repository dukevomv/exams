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

    public function getTasks() {
        return $this->tasks;
    }

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
        for ($t = 0; $t < count($this->tasks); $t++) {
            $type = $this->tasks[$t]['type'];
            $task = factory(Task::class)
                ->states([$type])
                ->create(
                    array_merge(
                        Arr::only($this->tasks[$t], ['description', 'points']),
                        [
                            'segment_id' => $segment->id,
                            'position'   => $position,
                        ]
                    ));
            $this->tasks[$t]['id'] = $task->id;
            $position++;

            switch ($type) {
                case TaskType::CMC:
                case TaskType::RMC:
                    $answerClass = [
                        TaskType::CMC => AnswerCmc::class,
                        TaskType::RMC => AnswerRmc::class,
                    ];
                    $commons = ['task_id' => $task->id];
                    $finalOptions = [];
                    if (!Arr::has($this->tasks[$t], 'options')) {
                        //if options doesnt exist create random amount of options
                        $finalOptions = factory($answerClass[$type], $this->faker->numberBetween(2, 10))->create($commons)->toArray();
                    } elseif (is_integer($this->tasks[$t]['options'])) {
                        //if options is a number, create that many options and ensure its more than 1
                        $amount = (integer)$this->tasks[$t]['options'];
                        $amount = ($amount <= 1) ? 2 : $amount;
                        $finalOptions = factory($answerClass[$type], $amount)->create($commons)->toArray();
                    } elseif (count($this->tasks[$t]['options']) > 0) {
                        //if options is an array
                        foreach ($this->tasks[$t]['options'] as $optionKey => $option) {
                            if (is_integer($optionKey) && (isset($option['description']) || isset($option['correct']))) {
                                //$optionKey is array with associative array in $option
                                $finalOptions[] = factory($answerClass[$type])->create(array_merge($commons, $option))->toArray();
                            } elseif (is_bool($option)) {
                                //$optionKey is the option description with correct boolean in $option
                                $finalOptions[] = factory($answerClass[$type])->create(array_merge($commons, [
                                    'description' => $optionKey,
                                    'correct'     => $option,
                                ]))->toArray();
                            }
                        }
                    }
                    $this->tasks[$t]['options'] = $finalOptions;
                    break;
                default:
            }
        }
    }
}