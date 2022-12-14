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
$factory->define(App\Models\Segments\Segment::class, function (Faker\Generator $faker) {
    return [
        'lesson_id' => function () {
            return factory(\App\Models\Lesson::class)->create()->id;
        },
        'title' => $faker->words(3,true),
        'description' => $faker->text,
    ];
});
