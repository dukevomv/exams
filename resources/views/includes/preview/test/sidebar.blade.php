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
            <p><strong>Lesson: </strong>{{ $test['lesson'] }}</p>
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
                    {{--                    todo make test list to show current status and active should be very visible for students - and then show grades on tests--}}
                    {{--                    todo add actions for grading and publishing grades for students--}}
                    <div class="margin-bottom-15 clearfix">
                        @if ($test['status'] == \App\Enums\TestStatus::PUBLISHED)
                            <button type="button" class="btn btn-success" id="start-test">Start in 30"
                            </button>
                        @elseif ($test['status'] == \App\Enums\TestStatus::STARTED)
                            <button type="button" class="btn btn-danger" id="finish-test">Finish in
                                30"
                            </button>
                        @elseif ($test['status'] == \App\Enums\TestStatus::FINISHED && array_key_exists('for_student',$test))
                            <button type="button" class="btn btn-success" id="publish-grade" @if(!$test['for_student']['publishable']) disabled @endif>Publish Grades
                            </button><button type="button" class="btn btn-primary pull-right" id="auto-grade"><i class="fa fa-save"></i> Save Auto
                            </button><br>
                        <!-- todo make  the below to be automatic save to all auto-calculated -->
                            <small><i class="fa fa-warning"></i> To publish student's grades you must first save each task's points.</small>
                        @endif
                    </div>
                @elseif (Auth::user()->role == 'student')
                    {{--                    todo here the save buttons dont work at all for any task  type--}}
                    <div class="margin-bottom-15 clearfix">
                        @if(!array_key_exists('current_user',$test))
                            @if (in_array($test['status'],['published','started']))
                                <form method="POST"
                                      action="{{URL::to('tests')}}/{{ $test['id'] }}/register">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <button type="submit" class="btn btn-success" id="test-register"
                                            @if( !$test['can_register'] || $test['status'] == 'started')  disabled="disabled" @endif >
                                        Register to Test
                                    </button>
                                </form>
                            @endif
                        @elseif($test['current_user']['status'] == 'registered')
                            @if ($test['status'] == 'published')
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
        @if (Auth::user()->role == 'professor' || isset($timer) && $test['status'] == \App\Enums\TestStatus::STARTED && !$timer['in_delay'])
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
</div>