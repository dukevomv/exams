@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="test-preview">
      <div class="row">
        <div class="sidebar col-xs-5 fixed-toolbar">
          <h3>
            {{$test->description}}
          </h3>
          <div id="segment-list" class="list-group">
            @foreach($test->segments as $key=>$segment)
              <a class="list-group-item list-group-item-action" href="#list-segment-id-{{$segment->id}}">{{$segment->title}} <small>({{count($segment->tasks)}} tasks)</small></a>
            @endforeach
          </div>
          <div class="panel panel-info">
            <div class="panel-heading">
              <h4>{{ $test->name }}</h4>
            </div>
            <div class="panel-body">
              <ul class="list-group">
                <li class="list-group-item">
                  <p><strong>Lesson: </strong>{{ $test->lesson->name }}</p>
                  <p><strong>Scheduled at: </strong>{{ !is_null($test->scheduled_at) ? $test->scheduled_at->format('d M, H:i') : '-'}}</p>
                  <p><strong>Total duration: </strong>@if(!is_null($test->duration)){{$test->duration}}'@endif</p>
                </li>
                <li class="list-group-item">
                  <div id="test-timer" class="test-timer"></div>
                </li>
              </ul>
              <div class="test-actions margin-top-30">
                @if (Auth::user()->role == 'professor')
                  <div class="margin-bottom-15 clearfix">
                    @if ($test->status == 'published')
                      <button type="button" class="btn btn-success" id="start-test">Start in 30"</button>
                    @elseif ($test->status == 'started')
                      <button type="button" class="btn btn-danger" id="finish-test">Finish in 30"</button>
                    @endif
                  </div>
                @elseif (Auth::user()->role == 'student')
                  <div class="margin-bottom-15 clearfix">
                    @if(!isset($test->user_on_test))
                      @if (in_array($test->status,['published','started']))
                        <form method="POST" action="{{URL::to('tests')}}/{{ $test->id }}/register">
                          <input type="hidden" name="_token" value="{{csrf_token()}}">
                          <button type="submit" class="btn btn-success" id="test-register" @if( !$test->can_register || $test->status == 'started')  disabled="disabled" @endif >Register to Test</button>
                        </form>
                      @endif
                    @elseif(isset($test->user_on_test) && $test->user_on_test->pivot->status == 'registered')
                      @if ($test->status == 'published')
                        <form method="POST" action="{{URL::to('tests')}}/{{ $test->id }}/leave" class="confirm-form" data-confirm-action="Leave" data-confirm-title="After leaving you will not be able to register again.">
                          <input type="hidden" name="_token" value="{{csrf_token()}}">
                          <button type="submit" class="btn btn-danger" id="test-leave">Leave Test</button>
                        </form>
                      @else
                        <button type="button" onClick="saveTest(true)" class="btn btn-success" id="test-save" @if(!$test->draft) disabled @endif>Submit  @if($test->draft) (1) @endif</button>
                        <button type="button" onClick="saveTest()" class="btn btn-warning pull-right" id="test-save-draft" disabled>Save as Draft</button>
                      @endif
                    @endif
                  </div>
                @endif
              </div>
            </div>
          </div>

          @if (Auth::user()->role == 'student' && $test->status == 'started' && isset($test->user_on_test) && $test->user_on_test->pivot->status == 'registered')
            <form method="POST" action="{{URL::to('tests')}}/{{ $test->id }}/leave" class="confirm-form" data-confirm-action="Leave" data-confirm-title="After leaving you will not be able to register again.">
              <input type="hidden" name="_token" value="{{csrf_token()}}">
              <button type="submit" class="btn btn-danger" id="test-leave">Leave Test</button>
            </form>
          @endif
        </div>

        <div class="main col-xs-8 pull-right main-panel">
            <div id="test-student-segments" data-spy="scroll" data-target="#segment-list" data-offset="0" >
              @foreach($test->segments as $key=>$segment)
                <div class="segment-tasks-wrap" id="list-segment-id-{{$segment->id}}">
                  <h4 class="clearfix">{{$segment->title}}</h4>
                  @foreach($segment->tasks as $task)
                    @include('includes.preview.segments.task_view_panel', ['task' => $task])
                  @endforeach
                  <hr>
                </div>
              @endforeach
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/test.js') }}"></script>
    <script type="text/javascript">
      testData.test = {!! json_encode($test) !!};
    </script>
@endsection