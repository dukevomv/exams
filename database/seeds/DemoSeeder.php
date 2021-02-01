<?php

use App\Enums\TaskType;
use App\Enums\TestUserStatus;
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
        $demoUserId = DB::table('demo_users')->insertGetId(['email' => $email, 'email_timestamp' => $timestamp]);

        $users = self::generateUsersForEmail($email, $timestamp, [
            UserRole::ADMIN     => 1,
            UserRole::PROFESSOR => 1,
            UserRole::STUDENT   => 16,
        ]);

        $lesson = self::createLessonForUsers($users);

        $testData = [
            [
                'status' => 'draft',
            ],
            [
                'status'    => 'published',
                'users'  => $users[UserRole::STUDENT],
                'published' => Carbon::now()->addMinutes(2),
            ],
            [
                'status'  => 'started',
                'users'  => $users[UserRole::STUDENT],
                'started' => Carbon::now()->addMinutes(2),
            ],
            [
                'status'  => 'started',
                'users'  => $users[UserRole::STUDENT],
                'started' => Carbon::now()->subMinutes(60),
            ],
            [
                'status'   => 'finished',
                'users'  => $users[UserRole::STUDENT],
                'finished' => Carbon::now()->addMinutes(2),
            ],
            [
                'status'   => 'finished',
                'users'  => $users[UserRole::STUDENT],
                'finished' => Carbon::now()->subMinutes(2),
            ],
        ];

        for ($t = 1; $t <= count($testData); $t++) {
            $tests[] = self::createPredefinedTest(array_merge([
                'lesson' => $lesson,
                'count'  => $t,
            ], $testData[$t - 1]));
        }

        DB::table('demo_users')->where('id', $demoUserId)->update(['finished' => true]);
        return $timestamp;
    }

    private static function createPredefinedTest($data) {
        $builder = TestBuilder::instance()
                              ->appendAttributes(['name' => $data['lesson']->name . ' no ' . $data['count']])
                              ->withSegmentTasks(self::getPredefinedSegment('random'))
                              ->withSegmentTasks(self::getPredefinedSegment('numbers'))
                              ->{$data['status']}()
                              ->inLesson($data['lesson']->id);

        for($u=0;$u<count($data['users']);$u++) {
            $builder->withUser($data['users'][$u]->id,[
                'created_at' => Carbon::now()->subMinutes($u*7),
                'status'     => TestUserStatus::REGISTERED,
            ]);
        }

        return $builder->build();
    }

    private static function generateUsersForEmail($email, $timestamp, $rolesCounts) {
        $userData = [];
        $users = [];

        if (!is_null($email)) {
            $name = Demo::generateNameFromEmail($email);
        }
        foreach ($rolesCounts as $role => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $generatedName = Demo::generateNameFromEmail($email);
                $generatedEmail = Demo::generateEmailForRole($timestamp, $role);
                if ($i > 1) {
                    $generatedEmail .= $i;
                    $generatedName = ucfirst($role) . ' ' . $generatedName . ' ' . $i;
                }
                $attr = [
                    'email' => $generatedEmail,
                    'name'  => $generatedName,
                ];
                if (!isset($userData[$role])) {
                    $userData[$role] = [];
                }
                $userData[$role][] = !is_null($email) ? $attr : [];
            }
        }

        foreach ($userData as $role => $data) {
            foreach ($data as $uData) {
                if (!isset($users[$role])) {
                    $users[$role] = [];
                }
                $users[$role][] = factory(User::class)->states([$role])->create($uData);
            }
        }
        return $users;
    }

    private static function newLessonId($users) {
        return self::createLessonForUsers($users)->id;
    }

    private static function createLessonForUsers($users) {
        return LessonBuilder::instance()
                            ->withUser($users[UserRole::PROFESSOR][0]->id)
                            ->withUser($users[UserRole::STUDENT][0]->id)
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
