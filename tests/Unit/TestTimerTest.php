<?php

namespace Tests\Unit;

use App\Models\Lesson;
use App\Models\User;
use Tests\TestCase;

class TestTimerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDummy() {
        $lesson = factory(\App\Models\Lesson::class)->create();
        $users = factory(\App\Models\User::class, 1)->create()
                                                    ->each(function ($user) use ($lesson) {
                                                        $user->lessons()->attach($lesson->id,[
                                                            'approved' => 1,
                                                        ]);

                                                    });
        $this->assertTrue(true);
    }

    public function testTimerOnPublishedIsFrozen() {}
    public function testTimerStartedInPreparationSeconds() {}
    public function testTimerStartedSubtractsFromDuration() {}
    public function testTimerFinishedInPreparationSeconds() {}
    public function testTimerFinishedIsFrozen() {}
}
