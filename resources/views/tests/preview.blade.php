@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="test-preview">
            <div class="row">
                @include('includes.preview.test.sidebar', ['test' => $test,'timer' => isset($timer)? $timer:null])
                <div class="main col-xs-8 pull-right main-panel">
                    @if ((Auth::user()->role == \App\Enums\UserRole::STUDENT
                            && (($test['status'] == \App\Enums\TestStatus::STARTED && !$timer['in_delay'])
                                || ($test['status'] == \App\Enums\TestStatus::FINISHED && $timer['in_delay'])))
                        || (Auth::user()->role == \App\Enums\UserRole::PROFESSOR && isset($forUser) && in_array($test['status'],[\App\Enums\TestStatus::FINISHED,\App\Enums\TestStatus::GRADED])))
                        <div id="test-student-segments" data-spy="scroll" data-target="#segment-list" data-offset="0">
                            @if(Auth::user()->role == \App\Enums\UserRole::PROFESSOR && isset($test['for_student']) && in_array($test['for_student']['status'],[\App\Enums\TestUserStatus::LEFT,\App\Enums\TestUserStatus::REGISTERED]))
                                <div class="alert alert-danger" role="alert">
                                    <b>Warning!</b> The current student has <b>not</b> participated in the test.
                                </div>
                            @endif
                            @foreach($test['segments'] as $segment)
                                <div class="segment-tasks-wrap" id="list-segment-id-{{$segment['id']}}">
                                    <h4 class="clearfix">{{$segment['title']}}
                                        @if(array_key_exists('total_given_points',$segment))
                                            <span class="pull-right">
                                                {{$segment['total_given_points']}}/{{$segment['total_points']}}
                                            </span>
                                        @endif
                                    </h4>

                                    @if(Auth::user()->role == \App\Enums\UserRole::PROFESSOR && isset($segment['changed']) && $segment['changed'])
                                        <div class="alert alert-warning" role="alert">
                                            <b>Warning!</b> This <a href="{{URL::to('segments/'.$segment['id'].'/preview')}}" target="_blank">Segment <i class="fa fa-external-link" aria-hidden="true"></i></a> has changed since the time this test was published.
                                        </div>
                                    @endif

                                    @foreach($segment['tasks'] as $task)
                                        @php
                                            $data = [
                                                'test_id' => $test['id']
                                            ];
                                            if(isset($forUser)){
                                                $data['student_id'] = $forUser;
                                            }
                                        @endphp
                                        @include('includes.preview.segments.task_view_panel', array_merge($data,['task' => $task]))
                                    @endforeach
                                    <hr>
                                </div>
                            @endforeach
                        </div>
                    @elseif (Auth::user()->role == 'professor')
                        @include('includes.preview.test.users_panel', ['users' => $test['users'],'testId'=>$test['id'],'stats' => isset($test['stats']) ? $test['stats'] : null])
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/test.js') }}"></script>
    <script type="text/javascript">
      testData.user = {!! json_encode(Auth::user()) !!};
      testData.test = {!! json_encode($test) !!};
      @if(isset($timer))
        testData.timer = {!! json_encode($timer) !!};
      testData.server_time = moment('{{$timer['server_time']}}');
      testData.serverSecondsDifference = moment().diff(testData.server_time, 'seconds');

      testUtils.initializeRealtime()
      testUtils.initiateTimer()
        @endif
    </script>
@endsection