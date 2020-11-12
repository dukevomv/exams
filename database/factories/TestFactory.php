<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Test::class, function (Faker\Generator $faker) {
    return [
        'lesson_id' => function () {
            return factory(\App\Models\Lesson::class)->create()->id;
        },
        'name' => $faker->name,
        'description' => $faker->text,
        'status' => $faker->randomElement(\App\Enums\TestStatus::values()),
        'scheduled_at' => null,
        'duration' => $faker->randomDigit,
    ];
});

foreach(App\Enums\TestStatus::values() as $status){
    $factory->state(App\Models\Test::class, $status, function (Faker\Generator $faker) use ($status){
        return [
            'status' => $status
        ];
    });
}
