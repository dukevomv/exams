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
const SUBJECTS = ['Accounting and Finance', 'Aeronautical and Manufacturing Engineering', 'Agriculture and Forestry',
    'Anatomy and Physiology', 'Anthropology', 'Archaeology', 'Architecture', 'Art and Design', 'Biological Sciences',
    'Building', 'Business and Management Studies', 'Chemical Engineering', 'Chemistry', 'Civil Engineering',
    'Classics and Ancient History', 'Communication and Media Studies', 'Complementary Medicine', 'Computer Science',
    'Counselling', 'Creative Writing', 'Criminology', 'Dentistry', 'Drama Dance and Cinematics', 'Economics',
    'Education', 'Electrical and Electronic Engineering', 'English', 'Fashion', 'Film Making', 'Food Science',
    'Forensic Science', 'General Engineering', 'Geography and Environmental Sciences', 'Geology',
    'Health And Social Care', 'History', 'History of Art Architecture and Design',
    'Hospitality Leisure Recreation and Tourism', 'Information Technology', 'Land and Property Management', 'Law',
    'Linguistics', 'Marketing', 'Materials Technology', 'Mathematics', 'Mechanical Engineering', 'Medical Technology',
    'Medicine', 'Music', 'Nursing', 'Occupational Therapy', 'Pharmacology and Pharmacy', 'Philosophy',
    'Physics and Astronomy', 'Physiotherapy', 'Politics', 'Psychology', 'Robotics', 'Social Policy', 'Social Work',
    'Sociology', 'Sports Science', 'Veterinary Medicine', 'Youth Work',
];

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Lesson::class, function (Faker\Generator $faker) {
    return [
        'name'       => $faker->randomElement(SUBJECTS),
        'semester'   => $faker->randomDigitNot(0),
        'gunet_code' => str_random(10),
    ];
});
