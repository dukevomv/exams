<?php

namespace Tests\Unit;

use App\Enums\TaskType;
use App\Enums\UserRole;
use App\Models\User;
use App\Services\TestServiceInterface;
use Tests\Builders\TestBuilder;
use Tests\TestCase;

class TestGraderTest extends TestCase {

    /**
     * @var TestServiceInterface
     */
    private $service;

    protected function setUp() {
        parent::setUp();
        $this->service = app()->make(TestServiceInterface::class);
    }

    public function testGraderCalculatesCmcAllSelectedEqualsZero() {
        $student = factory(User::class)->states(UserRole::STUDENT)->create();
        $test = TestBuilder::instance()->withUser($student->id)->withSegmentTasks([
            [
                'type'        => TaskType::CMC,
                'points'      => 3,
                'description' => 'select strings with a',
                'options'     => [
                    'dog'      => false,
                    'cat'      => true,
                    'rat'      => true,
                    'elephant' => true,
                    'fox'      => false,
                    'kitten'   => false,
                ],
                'answers'     => [
                    $student->id => [
                        'dog'      => true,
                        'cat'      => true,
                        'rat'      => true,
                        'elephant' => true,
                        'fox'      => true,
                        'kitten'   => true,
                    ],
                ],
            ],
        ])->build();

        $test = $this->service->calculateUserPoints($test, $student->id);
        $this->assertEquals(0,$test->segments[0]->tasks[0]->given_points);
    }

    public function testGraderCalculatesCmcNoneSelectedEqualsZero() {
        $student = factory(User::class)->states(UserRole::STUDENT)->create();
        $test = TestBuilder::instance()->withUser($student->id)->withSegmentTasks([
            [
                'type'        => TaskType::CMC,
                'points'      => 3,
                'description' => 'select strings with a',
                'options'     => [
                    'dog'      => false,
                    'cat'      => true,
                    'rat'      => true,
                    'elephant' => true,
                    'fox'      => false,
                    'kitten'   => false,
                ],
                'answers'     => [
                    $student->id => [
                        'dog'      => false,
                        'cat'      => false,
                        'rat'      => false,
                        'elephant' => false,
                        'fox'      => false,
                        'kitten'   => false,
                    ],
                ],
            ],
        ])->build();

        $test = $this->service->calculateUserPoints($test, $student->id);
        $this->assertEquals(0,$test->segments[0]->tasks[0]->given_points);
    }

    public function testGraderCalculatesCmcPartiallySelectedIsCorrect() {
        $student = factory(User::class)->states(UserRole::STUDENT)->create();
        $test = TestBuilder::instance()->withUser($student->id)->withSegmentTasks([
            [
                'type'        => TaskType::CMC,
                'points'      => 3,
                'description' => 'select strings with a',
                'options'     => [
                    'dog'      => false,
                    'cat'      => true,
                    'rat'      => true,
                    'elephant' => true,
                    'fox'      => false,
                    'kitten'   => false,
                ],
                'answers'     => [
                    $student->id => [
                        'dog'      => true,
                        'cat'      => true,
                        'rat'      => true,
                        'elephant' => true,
                        'fox'      => false,
                        'kitten'   => false,
                    ],
                ],
            ],
        ])->build();

        $test = $this->service->calculateUserPoints($test, $student->id);
        $this->assertEquals(2,$test->segments[0]->tasks[0]->given_points); // 2 = 3 points were given by 3 correct answers and 1 was subtracted from 1 wrong selected
    }

    public function testGraderCalculatesRmcWrongSelection() {
        $student = factory(User::class)->states(UserRole::STUDENT)->create();
        $test = TestBuilder::instance()->withUser($student->id)->withSegmentTasks([
            [
                'type'        => TaskType::RMC,
                'points'      => 3,
                'description' => 'select strings cat',
                'options'     => [
                    'dog'      => false,
                    'cat'      => true,
                    'rat'      => false,
                ],
                'answers'     => [
                    $student->id => [
                        'dog'      => true,
                        'cat'      => false,
                        'rat'      => false,
                    ],
                ],
            ],
        ])->build();

        $test = $this->service->calculateUserPoints($test, $student->id);
        $this->assertEquals(0,$test->segments[0]->tasks[0]->given_points);
    }

    public function testGraderCalculatesRmcCorrectSelection() {
        $student = factory(User::class)->states(UserRole::STUDENT)->create();
        $test = TestBuilder::instance()->withUser($student->id)->withSegmentTasks([
            [
                'type'        => TaskType::RMC,
                'points'      => 3,
                'description' => 'select strings cat',
                'options'     => [
                    'dog'      => false,
                    'cat'      => true,
                    'rat'      => false,
                ],
                'answers'     => [
                    $student->id => [
                        'dog'      => false,
                        'cat'      => true,
                        'rat'      => false,
                    ],
                ],
            ],
        ])->build();

        $test = $this->service->calculateUserPoints($test, $student->id);
        $this->assertEquals(3,$test->segments[0]->tasks[0]->given_points);
    }
    public function testGraderCalculatesRmcNullSelectionAsWrong() {
        $student = factory(User::class)->states(UserRole::STUDENT)->create();
        $test = TestBuilder::instance()->withUser($student->id)->withSegmentTasks([
            [
                'type'        => TaskType::RMC,
                'points'      => 3,
                'description' => 'select strings cat',
                'options'     => [
                    'dog'      => false,
                    'cat'      => true,
                    'rat'      => false,
                ],
                'answers'     => [
                    $student->id => [
                        'dog'      => false,
                        'cat'      => false,
                        'rat'      => false,
                    ],
                ],
            ],
        ])->build();

        $test = $this->service->calculateUserPoints($test, $student->id);
        $this->assertEquals(0,$test->segments[0]->tasks[0]->given_points);
    }

    public function testGraderCalculatesFreeTextThrowsError() {
    }

    public function testGraderCalculatesCorrespondence() {
    }
}
