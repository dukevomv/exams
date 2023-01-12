<?php

namespace Tests\Builders;

use App\Enums\TaskType;
use App\Models\Segments\AnswerCmc;
use App\Models\Segments\AnswerCorrespondence;
use App\Models\Segments\AnswerFreeText;
use App\Models\Segments\AnswerRmc;
use App\Models\Segments\Segment;
use App\Models\Segments\Task;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Tests\Builders\Traits\AddsLessonId;

class SegmentBuilder extends ModelBuilder {

    use AddsLessonId;

    const ANSWER_CLASS_MAP = [
        TaskType::CMC            => AnswerCmc::class,
        TaskType::RMC            => AnswerRmc::class,
        TaskType::CORRESPONDENCE => AnswerCorrespondence::class,
        TaskType::FREE_TEXT      => AnswerFreeText::class,
    ];

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

    private function createOptions($type, $data,$amount = 1) {
        $items = [];
        switch ($type) {
            case TaskType::RMC:
                $correct = rand(0,$amount-1);
                for ($i = 0; $i < $amount; $i++) {
                    $state = $i == $correct ? 'correct' : 'wrong';
                    $items[] = factory(self::ANSWER_CLASS_MAP[$type])->states([$state])->create($data)->toArray();
                }
                break;
            default:
                $items = factory(self::ANSWER_CLASS_MAP[$type], $amount)->create($data)->toArray();
        }
        return $items;
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

            $commons = ['task_id' => $task->id];
            switch ($type) {
                case TaskType::CMC:
                case TaskType::RMC:
                case TaskType::CORRESPONDENCE:
                    $finalOptions = [];
                    if (!Arr::has($this->tasks[$t], 'options')) {
                        //if options doesnt exist create random amount of options
                        $finalOptions = $this->createOptions($type, $this->faker->numberBetween(2, 10), $commons);
                    } elseif (is_integer($this->tasks[$t]['options'])) {
                        //if options is a number, create that many options and ensure its more than 1
                        $amount = (integer)$this->tasks[$t]['options'];
                        $amount = ($amount <= 1) ? 2 : $amount;
                        $finalOptions = $this->createOptions($type, $commons,$amount);
                    } elseif (count($this->tasks[$t]['options']) > 0) {
                        //if options is an array
                        foreach ($this->tasks[$t]['options'] as $optionKey => $option) {
                            if (is_integer($optionKey) && (is_object($option) || is_array($option)) && Arr::isAssoc($option)) {
                                //$optionKey is array with associative array in $option
                                $finalOptions[] = $this->createOptions($type, array_merge($commons, $option));
                            } else {
                                if (is_bool($option)) {
                                    //$optionKey is the option description with correct boolean in $option
                                    $finalOptions[] = $this->createOptions($type, array_merge($commons, [
                                        'description' => $optionKey,
                                        'correct'     => $option,
                                    ]));
                                } else {
                                    if ($type == TaskType::CORRESPONDENCE) {
                                        //$optionKey is the option description with correct boolean in $option
                                        $finalOptions[] = $this->createOptions($type, array_merge($commons, [
                                            'side_a' => $optionKey,
                                            'side_b' => $option,
                                        ]));
                                    }
                                }
                            }
                        }
                    }
                    $this->tasks[$t]['options'] = $finalOptions;
                    break;
                case TaskType::FREE_TEXT:
                    $this->tasks[$t]['answers'] = $this->createOptions($type, $commons);
                default:
            }
        }
    }
}