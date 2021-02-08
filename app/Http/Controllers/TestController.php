<?php

namespace App\Http\Controllers;

use App\Enums\General;
use App\Enums\TestStatus;
use App\Models\Lesson;
use App\Models\Test;
use App\Services\TestServiceInterface;
use Auth;
use Illuminate\Http\Request;
use Log;

class TestController extends Controller {

    protected $service;

    public function __construct(TestServiceInterface $service) {
        $this->service = $service;
    }

    public function index(Request $request) {
        $lessons = Lesson::approved()->get();
        $filters = $request->only(['search', 'lesson','status']);
        $filters['paginate'] = General::DEFAULT_PAGINATION;

        return view('tests.index', [
            'tests'   => $this->service->get($filters),
            'lessons' => $lessons,
        ]);
    }

    public function preview($id, Request $request) {
        $test = $this->service->setById($id);

        $data = ['test' => $this->service->prepareForCurrentUser()];
        if ($test->status !== TestStatus::GRADED) {
            $data['timer'] = $this->service->calculateTimer($test);
        }

        return view('tests.preview', $data);
    }

    public function lobby($id = null) {
        $test = Test::where('id', $id)->where('status', '!=', TestStatus::DRAFT)->with('lesson', 'users')->first();
        if (is_null($test)) {
            return redirect('tests');
        }
        return view('tests.lobby', ['test' => $test]);
    }
}
