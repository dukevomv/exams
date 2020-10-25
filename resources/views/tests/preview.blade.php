@extends('layouts.app')

@section('styles')
<style>
  .test-timer.alarm{
    color:#c75e71;
  }
  .fixed-toolbar{
    margin-top: 70px;
    max-width:350px;
  }
  #test-student-segments{
    position:relative;
  }
</style>
@endsection

@section('content')
  <div class="container">
    <div class="test-preview">
      <div class="row">
        <div class="sidebar col-xs-5 fixed-toolbar">
          <h3>
            {{$test->description}}
          </h3>
          @if (Auth::user()->role == 'student' && $test->status == 'started'  && $timer['actual_time'])
          <div id="segment-list" class="list-group">
            @foreach($test->segments as $key=>$segment)
              <a class="list-group-item list-group-item-action" href="#list-segment-id-{{$segment->id}}">{{$segment->title}} <small>({{count($segment->tasks)}} tasks)</small></a>
            @endforeach
          </div>
          @endif
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
                      @elseif (($test->status == 'started' && $timer['actual_time']) || ($test->status == 'finished' && !$timer['actual_time']))
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
        
        <div class="main col-xs-8 pull-right">
          <!-- started -->
          @if (Auth::user()->role == 'student' && $test->status == 'started'  && $timer['actual_time'])  
            <div id="test-student-segments" data-spy="scroll" data-target="#segment-list" data-offset="0" >
              @foreach($test->segments as $key=>$segment)
                <div class="segment-tasks-wrap" id="list-segment-id-{{$segment->id}}">
                  <h4 class="clearfix">{{$segment->title}}</h4>
                  @foreach($segment->tasks as $task)
                    @include('includes.preview.segments.task_types.'.$task->type, ['task' => $task])
                  @endforeach
                  <hr>
                </div>
              @endforeach
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

    $('body').scrollspy({ target: '#segment-list' })

    $('.task-value').on('change',function(){
      toggleButton($('#test-save'),'enable');
      toggleButton($('#test-save-draft'),'enable');
    })

    //timer part
    //{
      var current = {
        timer: {!! json_encode($timer) !!},
        user : {!! json_encode(Auth::user()) !!},
        test : {!! json_encode($test) !!},
        now  : moment('{{$now}}'),
        server_diff : moment().diff(moment('{{$now}}'),'seconds')
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
        setTimerTo(current.timer.seconds_gap)
        current.timer.running = true
        realtime.reloadOn(current.timer.seconds_gap);
        if(current.user.role == 'student' && !current.test.user_on_test)
          window.location.reload;
      });
      
      setTimerTo(current.timer.remaining_seconds);
      //dont reload if test havent finished auto
      if(!current.timer.actual_time);
        realtime.reloadOn(current.timer.remaining_seconds);
      
      realtime.on('test.finished',function(payload){
        setTimerTo(current.timer.seconds_gap)
        current.timer.running = true
        realtime.reloadOn(current.timer.seconds_gap);
      });
      
      function setTimerTo(seconds){
        current.timer.remaining_seconds = seconds;
        var minutes = Math.floor(seconds/60);
        var hours = Math.floor(minutes/60);
        var minutes = minutes%60;
        var seconds_left = seconds%60;
        var now = '';
        now = (hours < 10 ? '0' : '')+hours+':'+(minutes < 10 ? '0' : '')+minutes+':'+(seconds_left < 10 ? '0' : '')+seconds_left
        $('#test-timer').text(now);
        if(current.timer.actual_time)
          $('#test-timer').removeClass('alarm');
        else
          $('#test-timer').addClass('alarm');
      }
      var timer = setInterval(function(){
        if(current.timer.running)
          if(current.timer.remaining_seconds > 0)
            setTimerTo(--current.timer.remaining_seconds);
        if(!current.test.can_register && moment().add(current.server_diff,'seconds').isAfter(current.test.register_time)){
          current.test.can_register = true;
          $('#test-register').prop('disabled',false);
        }
      },1000);
      //}
    
    function toggleButton(button,action,title=''){
      switch(action){
        case 'disable':
          button.prop('disabled',true);
          break;
        case 'enable':
          button.prop('disabled',false);
          break;
        default:
          //code
      }
      if(title !== '')
        button.text(title);
    }

    //examination part
    function saveTest(final=false){
      let answers=[];

      $("#test-student-segments .task-wrap").each(function(index) {
        let task_type = $(this).attr('data-task-type')
        answers.push(GetTaskAnswers($(this),task_type))
      });

      $.post("{{URL::to('tests')}}/{{ $test->id }}/submit",{final: final?1:0,answers,_token:"{{csrf_token()}}"},function() {
        toggleButton($('#test-save'),'enable','Submit'+(final?'':' (1)'));
        toggleButton($('#test-save-draft'),'disable');

        if(final){
          toggleButton($('#test-save'),'disable');
        }
      });
      
      function GetDOMValue(element){
        let data = {};
        element.find('.task-value').each(function(i) {
          if($(this).attr('data-value-prop')){
            if($(this).attr('data-value-prop') == 'checked'){
              data[$(this).attr('data-key')] = $(this).is(":checked") ? 1 : 0;
            }
          } else if($(this).attr('data-value')){
            data[$(this).attr('data-key')] = $(this).attr('data-value');
          }
        });
        return data;
      }
      function GetTaskAnswers(element, task_type){
        let task = {
          id          : element.attr('data-task-id'),
          type        : task_type,
        }
        switch(task_type) {
          case "rmc":
          case "cmc":
            task.data = []
            element.find('.task-list .task-choice').each(function(i) {
              let choice = GetDOMValue($(this));
              task.data.push(choice);
            })
            break;
          case "free_text":
            task.data = element.find('textarea').val();
            break;
          case "correspondence":
            element.find('.task-list .task-choice').each(function(i) {
              let choice = {
                id            : $(this).find('input.choice-id').val().trim() != '' ? $(this).find('input.choice-id').val().trim() : null,
                side_a        : $(this).find('input.side-a').val(),
                side_b        : $(this).find('input.side-b').val()
              }
              if(choice.side_a != '' && choice.side_b != '')   task.data.push(choice)
            })
            break;
          case "code":
            //todo: fix this
            task.data.push({
              id          : element.find('.task-code input').val(),
              description : element.find('.task-code textarea').val()
            })
            break;
          default:
            //code block
        }
        return task
      }
    }
  </script>
  <script src="{{ asset('js/test.js') }}"></script>

@endsection