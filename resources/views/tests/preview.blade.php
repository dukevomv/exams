@extends('layouts.app')

@section('styles')
<style>
  
</style>
@endsection

@section('content')
  <div class="container">
    <div class="test-preview">
      <div class="row">
        <div class="sidebar col-xs-4">
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
                      @if ($test->status == 'published')
                        <form method="POST" action="{{URL::to('tests')}}/{{ $test->id }}/register">
                          <input type="hidden" name="_token" value="{{csrf_token()}}">
                          <button type="submit" class="btn btn-success" id="test-register">Register to Test</button>
                        </form>
                      @elseif ($test->status == 'started')
                        <button type="submit" class="btn btn-deafult" id="test-register" disabled="disabled">Register to Test</button>
                      @endif
                    @elseif(isset($test->user_on_test) && $test->user_on_test->pivot->status == 'registered')
                      @if ($test->status == 'published')
                        <form method="POST" action="{{URL::to('tests')}}/{{ $test->id }}/leave" class="confirm-form" data-confirm-action="Leave" data-confirm-title="After leaving you will not be able to register again.">
                          <input type="hidden" name="_token" value="{{csrf_token()}}">
                          <button type="submit" class="btn btn-danger" id="test-leave">Leave Test</button>
                        </form>
                      @elseif ($test->status == 'started')
                        <button type="button" class="btn btn-success">Submit Final</button>
                        <button type="button" class="btn btn-warning pull-right">Save Changes</button>
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
        
        <div class="main col-xs-8">
          <!-- started -->
          @if (Auth::user()->role == 'student' && $test->status == 'started')  
            <div id="test-student-segments">
              <div class="test-description">
                {{$test->description}}
              </div>
              <div class="panel-group">
                @foreach($test->segments as $key=>$segment)
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title clearfix">
                        <a data-toggle="collapse" href="#segment-id-{{$segment->id}}">{{$segment->title}} <small>({{count($segment->tasks)}} tasks)</small></a>
                      </h4>
                    </div>
                    <div id="segment-id-{{$segment->id}}" class="panel-collapse collapse">
                      <div class="panel-body">
                        @foreach($segment->tasks as $task)
                          @include('includes.preview.segments.task_types.'.$task->type, ['task' => $task])
                        @endforeach
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>  
            </div>
           @elseif (Auth::user()->role == 'professor')
            <div class="panel panel-default" id="test-registered-students">
              <table class="table">
                <tr>
                  <th>Student Name</th>
                  <th>Entered At</th>
                  <th>Status</th>
                  <th>Grade</th>
                  <th class="text-center">Action</th>
                </tr>
              </table>
            </div>
          @endif
        </div>
      </div>
      
      
    </div>
  </div>
@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/locale/en.js"></script>
  <script src="https://www.gstatic.com/firebasejs/5.8.1/firebase.js"></script>
  <script src="{{ asset('js/realtime.js') }}"></script>
  <script>
    var current = {
      time: {
        now : '{{$now}}',
        remaining_seconds: {{$remaining_seconds}},
        actual_time: {{$actual_time}},
        seconds_gap: {{$seconds_gap}},
      },
      user : {!! json_encode(Auth::user()) !!},
      test : {!! json_encode($test) !!},
    }
  
    $('#start-test').on('click',function(e){
      $.post("{{URL::to('tests')}}/{{ $test->id }}/start",{_token:"{{csrf_token()}}"},function() {
        $('#start-test').removeClass('btn-success').addClass('btn-default').prop('disabled',false)
      });
    })
    $('#finish-test').on('click',function(e){
      $.post("{{URL::to('tests')}}/{{ $test->id }}/finish",{_token:"{{csrf_token()}}"},function() {
        $('#finish-test').removeClass('btn-danger').addClass('btn-default').prop('disabled',false)
      });
    })
    
    realtime.on('student.registered',function(student){
      $("#test-registered-students .table").append('<tr data-id="'+student.id+'" class="student-'+student.id+'">\
        <td>'+student.name+'</td>\
        <td>'+student.registered_at+'</td>\
        <td><span class="label label-warning">Registered</span></td>\
        <td></td>\
        <td></td>\
      </tr>');
    });
    realtime.on('student.left',function(student){
      $("#test-registered-students .table tr.student-"+student.id).remove(); 
    });
    
    realtime.on('test.started',function(payload){
      //todo get difference on seconds
      if(current.user.role == 'student' && !current.test.user_on_test)
        window.location.reload;
    });
    
    if(current.test.status == 'started'){
        setTimerTo(current.time.remaining_seconds);
        if(!current.time.actual_time);
          realtime.reloadOn(current.time.remaining_seconds);
    }
    
    realtime.on('test.finished',function(payload){
      setTimerTo(current.time.seconds_gap)
      realtime.reloadOn(current.time.seconds_gap);
    });
    
    function setTimerTo(seconds){
      current.time.remaining_seconds = seconds;
      var minutes = Math.floor(seconds/60);
      var seconds_left = seconds%60;
      var now = '';
      now = (minutes < 10 ? '0' : '')+minutes+':'+(seconds_left < 10 ? '0' : '')+seconds_left
      $('#test-timer').text(now);
    }
    
    var timer = setInterval(function(){
      if(current.time.remaining_seconds > 0)
        setTimerTo(--current.time.remaining_seconds);
    },1000) 
  </script>
  <script src="{{ asset('js/test.js') }}"></script>

@endsection