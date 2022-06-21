<div class="sidebar col-xs-5 fixed-toolbar">

    @php
        $showBackButton = array_key_exists('for_student',$test);
        $userIsProfessor = Auth::user()->role === \App\Enums\UserRole::PROFESSOR;
        $userIsStudent = Auth::user()->role === \App\Enums\UserRole::STUDENT;
        $testIsPublished = $test['status'] === \App\Enums\TestStatus::PUBLISHED;
        $testIsStarted = $test['status'] === \App\Enums\TestStatus::STARTED;
        $testIsFinished = $test['status'] === \App\Enums\TestStatus::FINISHED;
        $testIsGraded = $test['status'] === \App\Enums\TestStatus::GRADED;
        $showResults = isset($test['stats']) && !array_key_exists('for_student',$test);
    @endphp

    @if($showBackButton)
        <span><a href="{{URL::to('/tests/'.$test['id'])}}"><i class="fa fa-arrow-left"></i> Back</a></span>
        <h3>{{$test['for_student']['name']}}</h3>
    @endif
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4>{{ $test['name'] }}</h4>
            <p>{{$test['description']}}</p>
        </div>
        <div class="panel-body">
            <p><strong>Course: </strong>{{ $test['lesson'] }}</p>
            <p><strong>Scheduled at: </strong>{{$test['scheduled_at']}}</p>
            <p><strong>Total duration: </strong>{{$test['duration']}}</p>
            <p><strong>Status: </strong>{{ucfirst($test['status'])}}</p>
            @php
                $gradesDOM = '';
                $total = 0;
                $total_given = 0;
                if($test['with_grades']){
                    $pointsDOM = '';
                    foreach($test['segments'] as $segment){
                        $total_given += $segment['total_given_points'];
                        $total += $segment['total_points'];
                    }
                        $gradesDOM = '<p><strong>Grades: </strong><span class="label label-default pull-right">'.$total_given.'/'.$total.'</span></p>';
                }
            @endphp
            {!! $gradesDOM !!}
            <li class="list-group-item test-timer-wrap hidden">
                <div id="test-timer" class="test-timer"></div>
            </li>
            <div class="test-actions margin-top-30">
                @if($userIsProfessor)
                    <div class="margin-bottom-15 clearfix">
                        @if($testIsPublished)
                            <button type="button" class="btn btn-success" id="start-test">Start in 30"
                            </button>
                        @elseif ($testIsStarted)
                            <button type="button" class="btn btn-danger" id="finish-test">Finish in
                                30"
                            </button>
                        @elseif ($testIsFinished && !$timer['in_delay'])
                            @if(array_key_exists('for_student',$test))
                                <button type="button" class="btn btn-success" id="publish-grade"
                                        @if(!$test['for_student']['publishable'] || !$test['for_student']['gradable']) disabled @endif>Publish Grades
                                </button>
                                <button type="button" class="btn btn-primary pull-right" id="auto-grade"
                                        @if(!$test['for_student']['gradable']) disabled @endif><i
                                            class="fa fa-save"></i> Save Auto
                                </button><br>
                                <small><i class="fa fa-warning"></i> To publish student's grades you must first save
                                    each task's points.</small>
                            @else
                                @if($userIsProfessor && $testIsFinished)
                                    <button type="button" class="btn btn btn-success margin-bottom-15" id="publish-test-grades" @if(!$test['grades_publishable']) disabled @endif>Publish Grades</button>
                                @endif
                                <button type="button" class="btn btn-primary" id="auto-calculate-test"
                                        @if(!$test['auto_calculative']) disabled @endif>Auto Calculate Grades
                                </button><br>
                                @if(!$test['auto_calculative'])
                                    <small><i class="fa fa-warning"></i> Test must not include task types that can not
                                        be auto graded in order to auto calculate all students' grades.</small>
                                @endif
                            @endif
                        @endif
                    </div>
                @elseif($userIsStudent)
                    <div class="margin-bottom-15 clearfix">
                        @if(!array_key_exists('current_user',$test))
                            @if (in_array($test['status'],[\App\Enums\TestStatus::PUBLISHED,\App\Enums\TestStatus::STARTED]))
                                <form method="POST"
                                      action="{{URL::to('tests')}}/{{ $test['id'] }}/register">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <button type="submit" class="btn btn-success" id="test-register"
                                            @if( !$test['can_register'] || $test['status'] == 'started')  disabled="disabled" @endif >
                                        Register to Test
                                    </button>
                                    @if(!$test['can_register'])
                                        <br>
                                        <small><i class="fa fa-warning"></i> In {{$test['register_time']->fromNow()}}
                                            you will be able to register to this test.</small>
                                    @endif
                                </form>
                            @endif
                        @else
                            @if ($testIsPublished)
                                <form method="POST" action="{{URL::to('tests')}}/{{ $test['id'] }}/leave"
                                      class="confirm-form" data-confirm-action="Leave"
                                      data-confirm-title="After leaving you will not be able to register again.">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <button type="submit" class="btn btn-danger" id="test-leave">Leave
                                        Test
                                    </button>
                                </form>
                            @elseif ($userIsStudent
                                    && (($testIsStarted && !$timer['in_delay'])
                                        || ($testIsFinished && $timer['in_delay'])))
                                <button type="button" class="btn btn-success"
                                        id="save-test" @if(!$test['current_user']['has_draft']) disabled @endif>
                                    Submit @if($test['current_user']['has_draft']) (1) @endif</button>
                                <button type="button"
                                        class="btn btn-warning pull-right" id="save-draft-test"
                                        disabled>Save as Draft
                                </button>
                            @endif
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @if (false || ($userIsProfessor && $testIsFinished) || (isset($timer) && $testIsStarted && !$timer['in_delay']))
            <div id="segment-list" class="list-group">
                @foreach($test['segments'] as $segment)
                    @php
                        $pointsDOM = '';
                        if($test['with_grades']){
                            $pointsDOM = '<span class="label label-default pull-right">'.$segment['total_given_points'].'/'.$segment['total_points'].'</span>';
                        }
                    @endphp
                    <a class="list-group-item list-group-item-action"
                       href="#list-segment-id-{{$segment['id']}}">{{$segment['title']}}
                        <small>({{count($segment['tasks'])}} tasks)</small> {!!$pointsDOM!!}</a>
                @endforeach
            </div>
        @endif
    </div>

    @if ($userIsStudent && $testIsStarted && isset($test->user_on_test) && $test->user_on_test->pivot->status == 'registered')
        <form method="POST" action="{{URL::to('tests')}}/{{ $test['id'] }}/leave" class="confirm-form"
              data-confirm-action="Leave"
              data-confirm-title="After leaving you will not be able to register again.">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <button type="submit" class="btn btn-danger" id="test-leave">Leave Test</button>
        </form>
    @endif
    @if($showResults)
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">Results</h3>
            </div>
            <div class="panel-body">
                <span>Participated: <b>{{$test['stats']['students']['participated']}}/{{$test['stats']['students']['total']}} students</b></span>
                <br>
                <span>Above 50%: <b>{{$test['stats']['students']['passed']}} students</b></span>
                <br>
                <br>
                <span>Graded: <b>{{$test['stats']['students']['graded']}}/{{$test['stats']['students']['total']}} students</b></span>
                <br>
                <span>Minimum: <b>{{$test['stats']['min']}}</b></span>
                <br>
                <span>Maximum: <b>{{$test['stats']['max']}}</b></span>
                <br>
                <span>Range: <b>{{$test['stats']['range']}}</b></span>
                <br>
                <span>Average: <b>{{$test['stats']['average']}}</b></span>
                <br>
                <span>Standard Deviation: <b>{{$test['stats']['standard_deviation']}}</b></span>
            </div>
        </div>

        <div class="panel">
            <div class="panel-body">
                <a href="{{URL::to('tests/'.$test['id'].'/export-csv')}}"><i class="fa fa-download"></i> Export grades in CSV</a>
            </div>
        </div>
    @endif
    @if(true)
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-info-circle"></i> Action Info</h3>
            </div>
            <ul class="list-group">
                @if($userIsStudent)
                    <li class="list-group-item">
                        <a class="text-muted" role="button" data-toggle="collapse" href="#collapseTimer" aria-expanded="true" aria-controls="collapseTimer"><b>Timer</b></a>
                        <div id="collapseTimer" class="collapse collapsed" role="tabpanel" aria-labelledby="collapseTimer">Start and Finish are initiated by the Professor only.<br><b>You can still submit your answers even when timer is 00:00 if the test is not Finished.</b></div>
                    </li>
                    <li class="list-group-item">
                        <a class="text-muted" role="button" data-toggle="collapse" href="#collapseLeave" aria-expanded="true" aria-controls="collapseLeave"><b>Leave Test</b></a>
                        <div id="collapseLeave" class="collapse collapsed" role="tabpanel" aria-labelledby="collapseLeave">Removes student from the current examination.<br><i>After leaving you will not be able to register again.</i></div>
                    </li>
                    <li class="list-group-item">
                        <a class="text-muted" role="button" data-toggle="collapse" href="#collapseSave" aria-expanded="true" aria-controls="collapseSave"><b>Save as Draft</b></a>
                        <div id="collapseSave" class="collapse collapsed" role="tabpanel" aria-labelledby="collapseSave">Saves your answers in order to not lose your current progress.<br><i>Answers can be saved multiple times throughout the test examination.</i></div>
                    </li>
                    <li class="list-group-item">
                        <a class="text-muted" role="button" data-toggle="collapse" href="#collapseSubmit" aria-expanded="true" aria-controls="collapseSubmit"><b>Submit</b></a>
                        <div id="collapseSubmit" class="collapse collapsed" role="tabpanel" aria-labelledby="collapseSubmit">Submits your answers in order to be graded.<br><i>Answers can be submitted multiple times throughout the test examination.</i></div>
                    </li>
                @elseif($userIsProfessor)
                    <li class="list-group-item">
                        <a class="text-muted" role="button" data-toggle="collapse" href="#collapseTimer" aria-expanded="true" aria-controls="collapseTimer"><b>Timer</b></a>
                        <div id="collapseTimer" class="collapse collapsed" role="tabpanel" aria-labelledby="collapseTimer">Start and Finish are initiated by the Professor only.<br><b>You can give extra time to your students by waiting after the timer has gone to 00:00.</b></div>
                    </li>
                    <li class="list-group-item">
                        <a class="text-muted" role="button" data-toggle="collapse" href="#collapseStart" aria-expanded="true" aria-controls="collapseStart"><b>Start in 30"</b></a>
                        <div id="collapseStart" class="collapse collapsed" role="tabpanel" aria-labelledby="collapseStart">Starts the examination process and shows the segment questions to students.<br><i>Has 30" delay for students to prepare.</i></div>
                    </li>
                    <li class="list-group-item">
                        <a class="text-muted" role="button" data-toggle="collapse" href="#collapseFinish" aria-expanded="true" aria-controls="collapseFinish"><b>Finish in 30"</b></a>
                        <div id="collapseFinish" class="collapse collapsed" role="tabpanel" aria-labelledby="collapseFinish">Finishes the test and stops students from being able to submit new answers.<br><i>Has 30" delay for students to prepare.</i></div>
                    </li>
                    <li class="list-group-item">
                        <a class="text-muted" role="button" data-toggle="collapse" href="#collapseAutoCalc" aria-expanded="true" aria-controls="collapseAutoCalc"><b>Auto Calculate Grades</b></a>
                        <div id="collapseAutoCalc" class="collapse collapsed" role="tabpanel" aria-labelledby="collapseAutoCalc">Calculates student's grades and saves them.<br><i>Available when Test does not include task types that can not be auto graded. (Free Text)</i></div>
                    </li>
                    <li class="list-group-item">
                        <a class="text-muted" role="button" data-toggle="collapse" href="#collapseAutoSave" aria-expanded="true" aria-controls="collapseAutoSave"><b>Save Auto</b></a>
                        <div id="collapseAutoSave" class="collapse collapsed" role="tabpanel" aria-labelledby="collapseAutoSave">Saves student answers only on auto-gradable task types.<br><i>Available only when previewing the a student's answers</i></div>
                    </li>
                    <li class="list-group-item">
                        <a class="text-muted" role="button" data-toggle="collapse" href="#collapsePublishGrades" aria-expanded="true" aria-controls="collapsePublishGrades"><b>Publish Grades</b></a>
                        <div id="collapsePublishGrades" class="collapse collapsed" role="tabpanel" aria-labelledby="collapsePublishGrades">Makes saved grades available for student to see.<br><i>To publish student's grades you must first save each task's points (blue button).</i></div>
                    </li>
                    <li class="list-group-item">
                        <a class="text-muted" role="button" data-toggle="collapse" href="#collapseExport" aria-expanded="true" aria-controls="collapseExport"><b>Export grades in CSV</b></a>
                        <div id="collapseExport" class="collapse collapsed" role="tabpanel" aria-labelledby="collapseExport">Downloads grades and student data into a CSV file</div>
                    </li>
                @endif
            </ul>
        </div>
    @endif

</div>