<div class="sidebar col-xs-5 fixed-toolbar">
    @if(array_key_exists('for_student',$test))
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
                @if (Auth::user()->role == 'professor')
                    <div class="margin-bottom-15 clearfix">
                        @if ($test['status'] == \App\Enums\TestStatus::PUBLISHED)
                            <button type="button" class="btn btn-success" id="start-test">Start in 30"
                            </button>
                        @elseif ($test['status'] == \App\Enums\TestStatus::STARTED)
                            <button type="button" class="btn btn-danger" id="finish-test">Finish in
                                30"
                            </button>
                        @elseif ($test['status'] == \App\Enums\TestStatus::FINISHED && !$timer['in_delay'])
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
                                @if(Auth::user()->role == 'professor' && $test['status'] == \App\Enums\TestStatus::FINISHED)
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
                @elseif (Auth::user()->role == 'student')
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
                        @elseif(in_array($test['current_user']['status'],[\App\Enums\TestUserStatus::REGISTERED,\App\Enums\TestUserStatus::PARTICIPATED]))
                            @if ($test['status'] == \App\Enums\TestStatus::PUBLISHED)
                                <form method="POST" action="{{URL::to('tests')}}/{{ $test['id'] }}/leave"
                                      class="confirm-form" data-confirm-action="Leave"
                                      data-confirm-title="After leaving you will not be able to register again.">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <button type="submit" class="btn btn-danger" id="test-leave">Leave
                                        Test
                                    </button>
                                </form>
                            @elseif (Auth::user()->role == \App\Enums\UserRole::STUDENT
                                    && (($test['status'] == \App\Enums\TestStatus::STARTED && !$timer['in_delay'])
                                        || ($test['status'] == \App\Enums\TestStatus::FINISHED && $timer['in_delay'])))
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
        @if ((Auth::user()->role == 'professor' && $test['status'] == \App\Enums\TestStatus::FINISHED) || (isset($timer) && $test['status'] == \App\Enums\TestStatus::STARTED && !$timer['in_delay']))
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

    @if (Auth::user()->role == 'student' && $test['status'] == 'started' && isset($test->user_on_test) && $test->user_on_test->pivot->status == 'registered')
        <form method="POST" action="{{URL::to('tests')}}/{{ $test['id'] }}/leave" class="confirm-form"
              data-confirm-action="Leave"
              data-confirm-title="After leaving you will not be able to register again.">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <button type="submit" class="btn btn-danger" id="test-leave">Leave Test</button>
        </form>
    @endif
        @if(isset($test['stats']) && !array_key_exists('for_student',$test))
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

</div>