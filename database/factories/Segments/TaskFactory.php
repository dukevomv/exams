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
$factory->define(App\Models\Segments\Task::class, function (Faker\Generator $faker) {
    return [
        'segment_id' => function () {
            return factory(\App\Models\Segments\Segment::class)->create()->id;
        },
        'type' => $faker->randomElement(App\Enums\TaskType::values()),
        'description' => $faker->text,
        'position' => 0,
        'points' => $faker->numberBetween(1,50),
    ];
});

foreach(App\Enums\TaskType::values() as $type){
    $factory->state(App\Models\Segments\Task::class, $type, function (Faker\Generator $faker) use ($type){
        return [
            'type' => $type
        ];
    });
}

$factory->define(App\Models\Segments\AnswerCmc::class, function (Faker\Generator $faker) {
    return [
        'task_id' => function () {
            return factory(\App\Models\Segments\Task::class)->create()->id;
        },
        'description' => $faker->text,
        'correct' => $faker->boolean,
    ];
});

$factory->define(App\Models\Segments\AnswerRmc::class, function (Faker\Generator $faker) {
    return [
        'task_id' => function () {
            return factory(\App\Models\Segments\Task::class)->create()->id;
        },
        'description' => $faker->text,
        'correct' => $faker->boolean,
    ];
});