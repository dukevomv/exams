<?php

use App\Enums\TaskType;
use App\Enums\UserRole;
use App\Models\User;
use App\Util\Demo;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Tests\Builders\LessonBuilder;
use Tests\Builders\TestBuilder;

class DemoSeeder extends Seeder {

    /**
     * @param null $email
     *
     * @return int
     * @throws \ReflectionException
     */
    public function run($email = null) {
        if (!config('app.demo.enabled')) {
            throw new ValidationException('Environment is not DEMO enabled. Use DEMO_ENABLED=true variable in your .env');
        }

        $timestamp = Carbon::now()->timestamp;
        $userRoles = UserRole::values();
        $userRoleData = [];
        $users = [];

        $demoUserId = DB::table('demo_users')->insertGetId(['email' => $email, 'email_timestamp' => $timestamp]);

        $nameData = [];
        if (!is_null($email)) {
            $nameData = ['name' => Demo::generateNameFromEmail($email)];
        }
        foreach ($userRoles as $role) {
            $userRoleData[$role] = !is_null($email) ? array_merge($nameData, [
                'email' => Demo::generateEmailForRole($timestamp, $role),
            ]) : [];
        }

        foreach ($userRoleData as $role => $data) {
            $users[$role] = factory(User::class)->states([$role])->create($data);
        }
        $lesson = self::createLessonForUsers($users);
        $testCount = 0;
        $draft = TestBuilder::instance()
                            ->appendAttributes(['name'=>$lesson->name.' no '.++$testCount])
                            ->draft()
                            ->withUser($users[UserRole::STUDENT]->id)
                            ->inLesson($lesson->id)
                            ->withSegmentTasks(self::getPredefinedSegment('numbers'))
                            ->withSegmentTasks(self::getPredefinedSegment('random'))
                            ->build();

        $published = TestBuilder::instance()
                                ->appendAttributes(['name'=>$lesson->name.' no '.++$testCount])
                                ->published(Carbon::now()->addMinutes(2))
                                ->withUser($users[UserRole::STUDENT]->id)
                                ->inLesson($lesson->id)
                                ->withSegmentTasks(self::getPredefinedSegment('numbers'))
                                ->withSegmentTasks(self::getPredefinedSegment('random'))
                                ->build();

        $started = TestBuilder::instance()
                              ->appendAttributes(['name'=>$lesson->name.' no '.++$testCount])
                              ->started(Carbon::now()->addMinutes(2))
                              ->withUser($users[UserRole::STUDENT]->id)
                              ->inLesson($lesson->id)
                              ->withSegmentTasks(self::getPredefinedSegment('numbers'))
                              ->withSegmentTasks(self::getPredefinedSegment('random'))
                              ->build();

        $started_expired = TestBuilder::instance()
                                      ->appendAttributes(['duration' => 60,'name'=>$lesson->name.' no '.++$testCount])
                                      ->started(Carbon::now()->subMinutes(60))
                                      ->withUser($users[UserRole::STUDENT]->id)
                                      ->inLesson($lesson->id)
                                      ->withSegmentTasks(self::getPredefinedSegment('numbers'))
                                      ->withSegmentTasks(self::getPredefinedSegment('random'))
                                      ->build();

        $finished = TestBuilder::instance()
                               ->appendAttributes(['name'=>$lesson->name.' no '.++$testCount])
                               ->finished(Carbon::now()->addMinutes(2))
                               ->withUser($users[UserRole::STUDENT]->id)
                               ->inLesson($lesson)
                               ->withSegmentTasks(self::getPredefinedSegment('numbers'))
                               ->withSegmentTasks(self::getPredefinedSegment('random'))
                               ->build();

        $finished_expired = TestBuilder::instance()
                                       ->appendAttributes(['name'=>$lesson->name.' no '.++$testCount])
                                       ->finished(Carbon::now()->subMinutes(2))
                                       ->withUser($users[UserRole::STUDENT]->id)
                                       ->inLesson($lesson)
                                       ->withSegmentTasks(self::getPredefinedSegment('numbers'))
                                       ->withSegmentTasks(self::getPredefinedSegment('random'))
                                       ->build();

        DB::table('demo_users')->where('id', $demoUserId)->update(['finished' => true]);
        return $timestamp;

        //todo make demo:
        // - give option to user by adding a name to generarte 3 users with email and pass and autogenerated tests and lessons
        // - in register form you can proceed with full demo generation OR manual explain what is generated
        // - going register manually on demo, you are always activated
        // - make cases for free-text and corrrespondence
        // - make able to generate new user emails if exists
        // demo user must apply name and have correct name: Duke Professor etc.
        // create pages to  inform businness logic and  demo utilities
    }

    private static function newLessonId($users) {
        return self::createLessonForUsers($users)->id;
    }
    private static function createLessonForUsers($users) {
        return LessonBuilder::instance()
                            ->withUser($users[UserRole::PROFESSOR]->id)
                            ->withUser($users[UserRole::STUDENT]->id)
                            ->build();
    }

    private static function getPredefinedSegment($type) {
        $segments = [
            'numbers' => [
                [
                    [
                        'type'        => TaskType::CMC,
                        'points'      => 3,
                        'description' => 'Odd numbers?',
                        'options'     => [
                            'A'   => false,
                            '3'   => true,
                            '17'  => true,
                            '2'   => false,
                            '170' => false,
                        ],
                    ],
                    [
                        'type'        => TaskType::RMC,
                        'points'      => 13,
                        'description' => 'Highest number',
                        'options'     => [
                            'A'   => false,
                            '3'   => false,
                            '17'  => false,
                            '2'   => false,
                            '170' => true,
                        ],
                    ],
                    [
                        'type'        => TaskType::FREE_TEXT,
                        'points'      => 4,
                        'description' => 'Explain differences for integer and float numbers.',
                    ],
                ],
            ],
            'random'  => [
                [
                    [
                        'type'        => TaskType::CMC,
                        'points'      => 4,
                        'description' => 'Second Odd numbers?',
                        'options'     => 5,
                    ],
                    [
                        'type'        => TaskType::CORRESPONDENCE,
                        'points'      => 23,
                        'description' => 'Connect the dots',
                        'options'     => 6,
                    ],
                    [
                        'type'        => TaskType::RMC,
                        'points'      => 14,
                        'description' => 'Second Choose numbers',
                        'options'     => 6,
                    ],
                ],
            ],
        ];
        return $segments[$type][0];
    }
}
