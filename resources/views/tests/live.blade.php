@extends('layouts.app')

@section('styles')
<style>
  .tooltip-inner {
    white-space:nowrap;
    max-width:none;
  }
  .sidebar{
    top:0px;
    padding:0;
    position:fixed;
    width:20%;
  }
  .sidebar h4{margin-bottom:0;}
  .sidebar h4,
  .sidebar h5{margin-top:0;}
  .scroll-timer{
    font-size:30px;
  }
  .progress-wrap{
    width:100%;
    margin:45px 0 15px;
  }
  .progress-wrap .all-line{
    float:left;
    background-color:#ccc;
    border-radius:50px;
    width:calc(100% - 40px);
    height:4px;
  }
  .progress-wrap .remain-line{
    float:left;
    text-align:right;
    background-color:#3097D1;
    border-radius:50px;
    width:0%;
    -webkit-transition: width 1s;
    transition: width 1s;
    height:4px;
  }
  .progress-wrap .start-icon,
  .progress-wrap .remain-line .remain-icon,
  .progress-wrap .finish-icon{
    width:20px;
    float:left;
    margin-top:-9px;
  }
  .progress-wrap .remain-line .remain-icon{
    color:#3097D1;
    width:auto;
    float:right;
    font-size:9px;
    margin-right:-7px;
    margin-top:-5px;
  }
  .divider hr{
    margin:15px 0;
    border-top:1px solid #ccc;
  }
  .segment-wrap{
    margin:0;
    list-style:none;
    padding:0;
  }
  .segment-wrap{
    list-style:none;
  }
  #autosave-results{
    color:#a0a0a0;
    margin-bottom: 15px;
  }
</style>
@endsection

@section('content')
  <div class="container">
    <div class="test-wrap row">
      <div class="sidebar">
        <div class="col-xs-12 scroll-timer text-center">00:00</div>
        <div class="col-xs-12 progress-wrap">
          <?php 
            $start = Carbon\Carbon::parse($test->started_at)->format('H:i');
            $finish = Carbon\Carbon::parse($test->started_at)->addMinutes($test->duration)->format('H:i');
            $h = (int)($test->duration/60);
            $m = (int)($test->duration%60);
            $remain = '';
            $remain .= $h > 0 ? $h.'h ': '';
            $remain .= $m > 0 ? $m.'m ': '';
            $remain .= 'left';
          ?>
          <span class="start-icon text-left"><i class="fa fa-clock-o" data-toggle="tooltip" data-placement="top" title="Started at {{$start}}" aria-hidden="true"></i></span>
          <span class="all-line">
            <span class="remain-line text-right">
              <span class="remain-icon" data-toggle="tooltip" data-placement="top" title="{{$remain}}"><i class="fa fa-circle" aria-hidden="true"></i></span>
            </span>
          </span>
          <span class="finish-icon text-right"><i class="fa fa-flag-checkered" data-toggle="tooltip" data-placement="top" title="Ends at {{$finish}}" aria-hidden="true"></i></span>
        </div>
        <h4 class="test col-xs-12">{{$test->name}}</h4>
        <h5 class="test col-xs-12">Lesson: {{$test->lesson->name}}</h5>
        <ul class="segment-wrap col-xs-12">
          @foreach($test->segments as $segment)
            <a href="#internal-segment-{{$segment->id}}"><li class="segment col-xs-12">{{$segment->title}}</li></a>
          @endforeach
        </ul>
        <div class="divider col-xs-12"><hr></div>
        <div class="actions col-xs-12">
          <div id="autosave-results">No changes made.</div>
          <button class="col-xs-12 btn btn-primary">Send</button>
        </div>
      </div>
      <div class="live col-xs-12 col-md-9 col-md-offset-3">
        @foreach($test->segments as $segment)
          <h1 id="internal-segment-{{$segment->id}}">{{$segment->title}}</h1>
          <p>{{$segment->description}}</p>
          @foreach($segment->tasks as $task)
            @include('includes.preview.segments.task_types.'.$task->type, ['task' => $task])
          @endforeach
        @endforeach
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="{{asset('js/moment.min.js')}}"></script>
  <script src="{{asset('js/autosave.js')}}"></script>
  <script>
    $('[data-toggle="tooltip"]').tooltip()

    let localStorageForUser = function (){
      if(test_user_status == 'registered')
        $.ajax({
          type: "POST",
          url: "{{url('tests/'.$test->id.'/live/start')}}",
          data: {_token:"{{csrf_token()}}"},
          success: function(data){
            if(data.success){
              localStorage.clear()
              $(".autosave-field").autoSave(startAutoSave,finishAutoSave);
            }
          }
        })
      else
        $(".autosave-field").autoSave(startAutoSave,finishAutoSave);
    }

    let updateRemainText = function (){
      let progress_percentage = 100 - ((diff/60)/duration)*100;
      let diff_in_minutes = diff/60
      let h = parseInt(diff_in_minutes/60)
      let m = parseInt(diff_in_minutes%60)      
      let remain = ''
      remain += h > 0 ? h + 'h ' : ''
      remain += m > 0 ? m + 'm ' : ''    
      remain += 'left'
      let timer = ((h >= 10) ? h : '0'+h) +':'+ ((m >= 10) ? m : '0'+m)
      $('.progress-wrap .remain-icon').prop('title',remain).tooltip('fixTitle')
      $('.scroll-timer').text(timer)
      $('.progress-wrap .remain-line').css('width',progress_percentage+'%')
    }

    let startAutoSave = function() {
      var time = new Date().getTime();
      $("#autosave-results").text("Saving...");
      console.log("Saving local changes...")
    }
    let finishAutoSave = function() {
      var time = new Date().getTime();
      $("#autosave-results").text("All local changes saved.");
      console.log("All changes saved local.",time)
    }

    const test_id = '{{$test->id}}'
    const test_user_status = '{{$test->user[0]->pivot->status}}'
    let local_storage_reset = '{{$test->user}}'
    let started_at = '{{$test->started_at}}'
    let duration = parseInt('{{$test->duration}}')
    let now = moment('{{Carbon\Carbon::now()->toDateTimeString()}}')
    let diff = moment(started_at).add({minutes:duration}).diff(now, 'seconds', true)

    localStorageForUser()

    const interval_delay = 60
    const timeout_delay = diff%60
    diff += 60 - timeout_delay
    updateRemainText()
    setTimeout(function(){
      diff -= interval_delay
      updateRemainText()
      setInterval(function(){
        diff -= interval_delay
        updateRemainText()
      },interval_delay*1000)
    },timeout_delay*1000)

    if(localStorage)
      finishAutoSave()
  </script>
@endsection