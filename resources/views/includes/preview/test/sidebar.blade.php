<div class="sidebar col-xs-5 fixed-toolbar">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4>{{ $test['name'] }}</h4>
            <p>{{$test['description']}}</p>
        </div>
        <div class="panel-body">
            <p><strong>Lesson: </strong>{{ $test['lesson'] }}</p>
            <p><strong>Scheduled
                    at: </strong>{{$test['scheduled_at']}}
            </p>
            <p><strong>Total duration: </strong>{{$test['duration']}}
            </p>
            <p><strong>Status: </strong>{{ucfirst($test['status'])}}
            </p>
            <li class="list-group-item test-timer-wrap hidden">
                <div id="test-timer" class="test-timer"></div>
            </li>
            <div class="test-actions margin-top-30">
                @if (Auth::user()->role == 'professor')
                    {{--                    todo add actions for grading and publishing grades for students--}}
                    <div class="margin-bottom-15 clearfix">
                        @if ($test['status'] == 'published')
                            <button type="button" class="btn btn-success" id="start-test">Start in 30"
                            </button>
                        @elseif ($test['status'] == 'started')
                            <button type="button" class="btn btn-danger" id="finish-test">Finish in
                                30"
                            </button>
                        @endif
                    </div>
                @elseif (Auth::user()->role == 'student')
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
                            @elseif (Auth::user()->role == 'student' && isset($timer) && (($test['status'] == 'started' && $timer['actual_time']) || ($test['status'] == 'finished' && !$timer['actual_time'])))
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
        @if (Auth::user()->role == 'student' && isset($timer) && $test['status'] == 'started'  && $timer['actual_time'])
            <div id="segment-list" class="list-group">
                @foreach($test['segments'] as $segment)
                    <a class="list-group-item list-group-item-action"
                       href="#list-segment-id-{{$segment['id']}}">{{$segment['title']}}
                        <small>({{count($segment['tasks'])}} tasks)</small></a>
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