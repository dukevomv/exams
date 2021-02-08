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
/*
 * todo
 * - create tests that contain student answers
 * - update a test's segments after its published date in order to show segment warning (make it finished as well)
 * - create tests that need manual grading
 * - create tests that can be auto graded from lobby
 * - create tests that are valid questions
 */
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
            UserRole::STUDENT   => 35,
        ]);

        $lesson = self::createLessonForUsers($users);

        $testData = [
            [
                'status' => \App\Enums\TestStatus::DRAFT,
            ],
            [
                'status'    => \App\Enums\TestStatus::PUBLISHED,
                'users'  => $users[UserRole::STUDENT],
                'published' => Carbon::now()->addMinutes(2),
            ],
            [
                'status'  => \App\Enums\TestStatus::STARTED,
                'users'  => $users[UserRole::STUDENT],
                'started' => Carbon::now()->addMinutes(2),
            ],
            [
                'status'  => \App\Enums\TestStatus::STARTED,
                'users'  => $users[UserRole::STUDENT],
                'started' => Carbon::now()->subMinutes(60),
            ],
            [
                'status'   => \App\Enums\TestStatus::FINISHED,
                'users'  => $users[UserRole::STUDENT],
                'finished' => Carbon::now()->addMinutes(2),
            ],
            [
                'status'   => \App\Enums\TestStatus::FINISHED,
                'users'  => $users[UserRole::STUDENT],
                'finished' => Carbon::now()->subMinutes(2),
            ],
            [
                'status'   => \App\Enums\TestStatus::GRADED,
                'users'  => $users[UserRole::STUDENT],
                'graded' => Carbon::now()->addMinutes(5),
            ],
            [
                'status'   => \App\Enums\TestStatus::GRADED,
                'users'  => $users[UserRole::STUDENT],
                'graded' => Carbon::now()->subMinutes(5),
            ],
        ];

        for ($t = 0; $t < count($testData); $t++) {
            $tests[] = self::createPredefinedTest(array_merge([
                'lesson' => $lesson,
                'count'  => $t+1,
            ], $testData[$t]));
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

        if(isset($data['users'])){
            for($u=0;$u<count($data['users']);$u++) {
                $builder->withUser($data['users'][$u]->id,[
                    'created_at' => Carbon::now()->subMinutes($u*7),
                    'status'     => self::getRandomStudentStatusBasedOnTestStatus($data['status']),
                ]);
            }
        }

        return $builder->build();
    }

    private static function getRandomStudentStatusBasedOnTestStatus($testStatus){
        $map = [
          \App\Enums\TestStatus::PUBLISHED => [TestUserStatus::REGISTERED,TestUserStatus::LEFT],
          \App\Enums\TestStatus::STARTED => [TestUserStatus::REGISTERED,TestUserStatus::LEFT,TestUserStatus::PARTICIPATED],
          \App\Enums\TestStatus::FINISHED => [TestUserStatus::REGISTERED,TestUserStatus::LEFT,TestUserStatus::PARTICIPATED],
          \App\Enums\TestStatus::GRADED => [TestUserStatus::REGISTERED,TestUserStatus::LEFT,TestUserStatus::PARTICIPATED,TestUserStatus::GRADED],
        ];

        if(!isset($map[$testStatus])){
            return null;
        }

        return $map[$testStatus][rand ( 0 , count($map[$testStatus])-1 )];
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
                    ]
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
