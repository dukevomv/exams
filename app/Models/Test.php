<?php

namespace App\Models;

use App\Enums\TestStatus;
use App\Enums\TestUserStatus;
use App\Exceptions\InvalidOperationException;
use App\Models\Segments\Segment;
use App\Traits\Searchable;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Test extends Model {

    use Searchable;

    private   $search   = ['name'];
    protected $appends  = ['user_on_test', 'can_register', 'register_time'];
    public    $fillable = ['lesson_id', 'name', 'description', 'scheduled_at', 'duration', 'status'];
    protected $dates    = ['scheduled_at', 'started_at', 'finished_at', 'graded_at'];

    public function lesson() {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * @return $this
     */
    public function calculatePoints() {
        for ($i = 0; $i < count($this->segments->tasks); $i++) {
            $this->segments->tasks[$i]->calculatePoints();
        }
        return $this;
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeWithSegmentTaskAnswers($query) {
        return $query->with(['segments' => function ($q) {
            $q->withTaskAnswers();
        },
        ]);
    }

    public function segments() {
        return $this->belongsToMany(Segment::class)->orderBy('position', 'asc')->withTimestamps();
    }

    private function user_test_relation_query() {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('status', 'left_at', 'answers', 'answers_draft', 'answered_draft_at', 'answered_at', 'grades', 'graded_at', 'graded_by', 'given_points', 'total_points', 'grade_published_at')->using('App\Models\TestUser');
    }

    public function users() {
        return $this->user_test_relation_query();
    }

    public function user() {
        return $this->getUserById(Auth::id());
    }

    public function getUserById($userId) {
        return $this->user_test_relation_query()->where('user_id', $userId);
    }

    public function getUser($userId) {
        return $this->getUserById($userId)->first();
    }

    public function started_by() {
        return $this->belongsTo(User::class, 'started_by_user');
    }

    public function finished_by() {
        return $this->belongsTo(User::class, 'finished_by_user');
    }

    public function getUserOnTestAttribute() {
        return $this->getUser(Auth::id());
    }

    public function getRegisterTimeAttribute() {
        return is_null($this->scheduled_at) ? $this->scheduled_at : Carbon::parse($this->scheduled_at)->subMinutes(config('app.bm.test_register_before_scheduled_in_minutes'));
    }

    public function getCanRegisterAttribute() {
        return Carbon::now()->gte($this->register_time) && $this->status == TestStatus::PUBLISHED;
    }

    public function register() {
        $firebase = app('firebase');
        $student = Auth::user();
        $firebase->update([
            'name'          => $student->name,
            'registered_at' => Carbon::now()->toDateTimeString(),
        ], 'tests/' . $this->id . '/students/' . $student->id);

        $this->users()->attach(Auth::id(), ['status' => TestUserStatus::REGISTERED]);
    }

    public function leave() {
        $firebase = app('firebase');

        $student = Auth::user();

        $firebase->delete('tests/' . $this->id . '/students/' . $student->id);

        $this->users()->updateExistingPivot($student->id, ['status'  => TestUserStatus::LEFT, 'left_at' => Carbon::now(),
        ]);
    }

    public function getStudentsAnswers($userID, $final = false) {
        return $this->users()->where('user_id', $userID)->select();
    }

    public function publishProfessorGrade($userID) {
        return $this->users()->updateExistingPivot($userID, [
            'status'             => TestUserStatus::GRADED,
            'grade_published_at' => Carbon::now(),
        ]);
    }

    public function saveProfessorGrade($userID, array $grades, $given, $total) {
        return $this->users()->updateExistingPivot($userID, [
            'status'       => TestUserStatus::PARTICIPATED,
            'grades'       => json_encode($grades),
            'graded_at'    => Carbon::now(),
            'graded_by'    => Auth::id(),
            'given_points' => $given,
            'total_points' => $total,
        ]);
    }

    public function saveStudentsAnswers($userID, array $answers, $final = false) {
        $studentCanSave = $this->status == TestStatus::STARTED
            || ($this->status == TestStatus::FINISHED && Carbon::now()->lte(Carbon::parse($this->finished_at)));
        if (!$studentCanSave) {
            throw new InvalidOperationException('Test is not saveable', []);
        }
        return $this->users()->updateExistingPivot($userID, $this->constructAnswersFields($answers, $final));
    }

    private function constructAnswersFields(array $answers, $final) {
        $field_data = 'answers';
        $field_date = 'answered';

        if (!$final) {
            $field_data .= '_draft';
            $field_date .= '_draft';
        }
        $field_date .= '_at';

        return [
            $field_data => json_encode($answers),
            $field_date => Carbon::now(),
        ];
    }

    public function start() {
        $this->status = TestStatus::STARTED;
        $this->started_at = Carbon::now()->addSeconds(config('app.bm.test_timer.start_delay_in_seconds'));
        $this->started_by_user = Auth::id();

        if (config('services.firebase.enabled')) {
            $firebase = app('firebase');

            $firebase->update([
                'started_at' => $this->started_at->toDateTimeString(),
            ], 'tests/' . $this->id);
        }

        $this->save();
    }

    public function finish() {
        $this->status = TestStatus::FINISHED;
        $this->finished_at = Carbon::now()->addSeconds(config('app.bm.test_timer.finish_delay_in_seconds'));
        $this->finished_by_user = Auth::id();

        if (config('services.firebase.enabled')) {
            $firebase = app('firebase');

            $firebase->update([
                'finished_at' => $this->finished_at->toDateTimeString(),
            ], 'tests/' . $this->id);
        }

        $this->save();
    }
}
