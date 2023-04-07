<?php

use App\Enums\TaskType;
use App\Enums\TestUserStatus;
use App\Enums\UserRole;
use App\Models\Demo\TrialUser;
use App\Models\Trial\Trial;
use App\Models\User;
use App\Util\Demo;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Tests\Builders\LessonBuilder;
use Tests\Builders\TestBuilder;

class TrialSeeder extends Seeder {
    /**
     * @param null $email
     *
     * @throws \ReflectionException
     */
    public function run($trialId = null) {
        if (!config('app.trial.enabled')) {
            throw new ValidationException('Environment is not TRIAL enabled. Use TRIAL_ENABLED=true variable in your .env');
        }
        $trial = Trial::find($trialId);

        Session::put(config('app.trial.session_field'), $trial->id);

        $users = self::generateUsersForEmail($trial, $trial->uuid, [
//            UserRole::ADMIN     => 1,
            UserRole::PROFESSOR => 1,
//            UserRole::STUDENT   => 1,
        ]);

        $lesson = self::createLessonForUsers($trial,$users);

        $testData = [
                'name' => $lesson->name . ' Examination',
                'status' => \App\Enums\TestStatus::PUBLISHED,
                'scheduled_at' => Carbon::createFromFormat('Y-m-d\TH:i', \Illuminate\Support\Arr::get($trial->details,'scheduled_at')),
                'duration' => \Illuminate\Support\Arr::get($trial->details,'duration_in_minutes',60),
                'description' => 'The '.$lesson->name.' Trial Examination. Feel free to update this text.',
                'lesson_id' => $lesson->id,
        ];

        $tests[] = self::createPredefinedTest($testData);

        $trial->update(['seeded' => true]);
        return $trial->id;
    }

    private static function createPredefinedTest($data) {
        $builder = TestBuilder::instance()
                                ->withAttributes(\Illuminate\Support\Arr::except($data,['status','lesson_id']))
                                ->{$data['status']}(\Illuminate\Support\Arr::get($data,'scheduled_at'))
                                ->inLesson($data['lesson_id']);
        return $builder->build();
    }

    private static function generateUsersForEmail($trial, $postfix, $rolesCounts) {
        $userData = [];
        $users = [];

        if (!is_null($trial->email)) {
            $name = Trial::generateNameFromEmail($trial->email);
        }
        foreach ($rolesCounts as $role => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $generatedName = Trial::generateNameFromEmail($trial->email);
                $generatedEmail = Trial::generateEmailForRole($postfix, $role);
                if ($i > 1) {
                    $generatedEmail .= $i;
                    $generatedName = ucfirst($role) . ' ' . $generatedName . ' ' . $i;
                }
                $attr = [
                    'email' => $generatedEmail,
                    'name'  => $generatedName,
                    'otp_enabled'  => 1,
                ];
                if (!isset($userData[$role])) {
                    $userData[$role] = [];
                }
                $userData[$role][] = !is_null($trial->email) ? $attr : [];
            }
        }

        foreach ($userData as $role => $data) {
            foreach ($data as $uData) {
                if (!isset($users[$role])) {
                    $users[$role] = [];
                }
                $item = factory(User::class)->states([$role])->create($uData);
                $users[$role][] = $item;
            }
        }
        return $users;
    }

    private static function newLessonId($trial,$users) {
        return self::createLessonForUsers($trial,$users)->id;
    }

    private static function createLessonForUsers($trial,$users) {
        $lesson = LessonBuilder::instance()
                            ->appendAttributes([
                                'name' => \Illuminate\Support\Arr::get($trial->details,'course_name','Course'),
                                'gunet_code' => 'trial-'.$trial->uuid,
                            ])
                            ->withUser($users[UserRole::PROFESSOR][0]->id)
                            ->build();
        return $lesson;
    }
}
