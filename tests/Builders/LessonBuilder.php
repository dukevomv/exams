<?php

namespace Tests\Builders;

use App\Models\Lesson;
use App\Models\Test;
use Illuminate\Support\Arr;

/**
 * Class TestBuilder
 *
 * @package Tests\Builders
 */
class LessonBuilder extends ModelBuilder {

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
    private $users = [];
    /**
     * Adds user on test (pivot).
     *
     * @param $userId
     * @param array $pivot Pivot data, Defaults to ['status'=>'registered']
     *
     * @return $this
     */
    public function withUser($userId, $approved = true) {
        $this->users[$userId] = ['approved' => $approved ? 1 : 0];
        return $this;
    }

    /**
     * @return Test
     */
    public function build() {
        $attrs = array_merge([], $this->attributes);

        $lesson = factory(Lesson::class)->create($attrs);

        foreach ($this->users as $userId => $pivot) {
            $lesson->users()->attach($userId, Arr::only($pivot, ['approved']));
        }

        return $lesson;
    }
}