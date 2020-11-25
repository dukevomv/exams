@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="test-preview">
            <div class="row">
                @include('includes.preview.test.sidebar', ['test' => $test])
                <div class="main col-xs-8 pull-right main-panel">
                    @if ((Auth::user()->role == 'student' && $test['status'] == 'started'  && $timer['actual_time'])
                        || (Auth::user()->role == 'professor' && isset($forUser) && in_array($test['status'],['finished','graded'])))
                        <div id="test-student-segments" data-spy="scroll" data-target="#segment-list" data-offset="0">
                            @foreach($test['segments'] as $segment)
                                <div class="segment-tasks-wrap" id="list-segment-id-{{$segment['id']}}">
                                    <h4 class="clearfix">{{$segment['title']}}</h4>
                                    @foreach($segment['tasks'] as $task)
                                        @include('includes.preview.segments.task_view_panel', ['task' => $task])
                                    @endforeach
                                    <hr>
                                </div>
                            @endforeach
                        </div>
                    @elseif (Auth::user()->role == 'professor')
                        @include('includes.preview.test.users_panel', ['users' => $test['users'],'testId'=>$test['id']])
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/test.js') }}"></script>
    <script type="text/javascript">
      testData.test = {!! json_encode($test) !!};
      @if(isset($timer))
          testData.timer = {!! json_encode($timer) !!};
          testData.now = moment('{{$now}}');
          testData.serverSecondsDifference = moment().diff(moment('{{$now}}'), 'seconds');

          testUtils.initializeRealtime()
          testUtils.initiateTimer()
      @endif
    </script>
@endsection