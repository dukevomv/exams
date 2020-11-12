<?php

namespace App\Models;

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

    public function scopeWithSegmentTaskAnswers($query, $correct = false) {
        return $query->with(['segments' => function ($q) use ($correct) {
            $q->withTaskAnswers($correct);
        },
        ]);
    }

    public function segments() {
        return $this->belongsToMany(Segment::class)->orderBy('position', 'asc')->withTimestamps();
    }

    public function users() {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('status');
    }

    public function user() {
        return $this->getUserById(Auth::id());
    }

    public function getUserById($userId) {
        return $this->belongsToMany(User::class)->where('user_id', $userId)->withTimestamps()->withPivot('status', 'answers', 'answers_draft', 'answered_draft_at', 'answered_at', 'grade')->using('App\Models\TestUser');
    }

    public function getUser($userId) {
        return $this->getUserById($userId)->first();
    }

    public function mergeMyAnswersToTest() {
        return $this->mergeUserAnswersToTest(Auth::id());
    }

    public function mergeUserAnswersToTest($id) {
        $user = $this->getUser($id);
        if (is_null($user)) {
            return $this;
        }
        $field = 'answers';
        $this->draft = false;

        $answers = $user->pivot->{$field};
        if ($answers) {
            for ($s = 0; $s < count($this->segments); $s++) {
                for ($t = 0; $t < count($this->segments[$s]->tasks); $t++) {
                    foreach ($answers as $answer) {
                        if ($this->segments[$s]->tasks[$t]->id == $answer['id']) {
                            switch ($this->segments[$s]->tasks[$t]->type) {
                                case 'cmc':
                                case 'rmc':
                                    for ($c = 0; $c < count($this->segments[$s]->tasks[$t]->{$answer['type']}); $c++) {
                                        foreach ($answer['data'] as $answeredChoice) {
                                            if ($answeredChoice['id'] == $this->segments[$s]->tasks[$t]->{$answer['type']}[$c]->id) {
                                                $this->segments[$s]->tasks[$t]->{$answer['type']}[$c]->selected = $answeredChoice['correct'];
                                            }
                                        }
                                    }
                                    break;
                                case 'free_text':
                                    $this->segments[$s]->tasks[$t]->answer = $answer['data'];
                                    break;
                                default:
                                    //code
                            }
                        }
                    }
                }
            }
        }

        return $this;
    }


    public function started_by() {
        return $this->belongsTo(User::class, 'started_by_user');
    }

    public function finished_by() {
        return $this->belongsTo(User::class, 'finished_by_user');
    }

    public function getUserOnTestAttribute() {
        return $this->users()->where('user_id', Auth::id())->first();
    }

    public function getRegisterTimeAttribute() {
        return is_null($this->scheduled_at) ? $this->scheduled_at : Carbon::parse($this->scheduled_at)->subMinutes(30);
    }

    public function getCanRegisterAttribute() {
        return Carbon::now()->gte($this->register_time);
    }

    public function register() {
        $firebase = app('firebase');
        $student = Auth::user();
        $firebase->update([
            'name'          => $student->name,
            'registered_at' => Carbon::now()->toDateTimeString(),
        ], 'tests/' . $this->id . '/students/' . $student->id);

        $this->users()->attach(Auth::id(), ['status' => 'registered']);
    }

    public function leave() {
        $firebase = app('firebase');

        $student = Auth::user();

        $firebase->delete('tests/' . $this->id . '/students/' . $student->id);

        $this->users()->updateExistingPivot($student->id, ['status' => 'left']);
    }

    public function getStudentsAnswers($userID, $final = false) {
        return $this->users()->where('user_id', $userID)->select();
    }

    public function saveStudentsAnswers($userID, array $answers, $final = false) {
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
        $this->status = 'started';
        $this->started_at = Carbon::now()->addSeconds(30);
        $this->started_by_user = Auth::id();

        $firebase = app('firebase');
        $student = Auth::user();

        $firebase->update([
            'started_at' => Carbon::now()->toDateTimeString(),
        ], 'tests/' . $this->id);

        $this->save();
    }

    public function finish() {
        $this->status = 'finished';
        $this->finished_at = Carbon::now()->addSeconds(30);
        $this->finished_by_user = Auth::id();

        $firebase = app('firebase');
        $student = Auth::user();

        $firebase->update([
            'finished_at' => Carbon::now()->toDateTimeString(),
        ], 'tests/' . $this->id);

        $this->save();
    }
}
