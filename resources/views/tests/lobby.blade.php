@extends('layouts.app')

@section('styles')
<style>
  .navbar{margin:0;}
  .announcement-wrap{
    height:350px;
    padding:50px 0;
    background:#003dff;
  }
  .announcement{
    color:#fff;
    font-size:30px;
    text-align:center;
  }
  .announcement .icon .fa{
    font-size:80px;
    display:block;
    margin:30px 0;
  }
  .specs{
    padding: 25px 0;
  }
  .specs .spec-wrap{
    margin: 25px 0;
    text-align:center;
  }
  .specs .spec-wrap .value{
    font-size: 20px;
  }
</style>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="row announcement-wrap">
      <div class="announcement">
        <div class="test-name"><i>{{$test->name}}</i></div>
        <div class="icon"><i class="fa fa-spinner fa-pulse"></i></div>
        <div class="description"></div>
        <div class="action-wrap">
          @if(Auth::user()->role == 'professor')
            @if($test->status == 'published')
              <a href="{{url('tests/'.$test->id.'/start')}}" class="action action-{{$test->status}} btn btn-success">Start Now</a>
            @elseif($test->status == 'started')
              <a href="{{url('tests/'.$test->id.'/finish')}}" class="action action-{{$test->status}} btn btn-danger">Finish Now</a>
            @elseif($test->status == 'finished')
              <a href="{{url('tests/'.$test->id.'/grade')}}" class="action action-{{$test->status}} btn btn-default">Grades</a>
            @endif
          @elseif(Auth::user()->role == 'student')
            @if($test->status == 'published')
              <a href="{{url('tests/'.$test->id.'/register')}}" class="action action-{{$test->status}} btn btn-success">Register</a>
            @elseif($test->status == 'started')
              <a href="{{url('tests/'.$test->id.'/live')}}" class="action action-{{$test->status}} btn btn-default">Go to Test</a>
            @endif
          @endif
        </div>
      </div>
    </div>   
    <!-- 
    <i class="fa fa-inbox" aria-hidden="true"></i>
    <i class="fa fa-graduation-cap" aria-hidden="true"></i>
    <i class="fa fa-book" aria-hidden="true"></i>
    <i class="fa fa-pie-chart" aria-hidden="true"></i>
    
    <i class="fa fa-paper-plane" aria-hidden="true"></i> 
     -->

    <div class="container">
      <div class="col-xs-12 specs">
        <div class="spec-wrap col-xs-12 col-sm-6 col-md-3">
          <span class="title">Date</span><br>
          <span class="value">{{Carbon\Carbon::parse($test->scheduled_at)->toFormattedDateString()}}</span>
        </div>
        <div class="spec-wrap col-xs-12 col-sm-6 col-md-3">
          <span class="title">Start Time</span><br>
          <span class="value">{{Carbon\Carbon::parse($test->scheduled_at)->format('H:i')}}</span>
        </div>
        <div class="spec-wrap col-xs-12 col-sm-6 col-md-3">
          <span class="title">Duration</span><br>
          <span class="value">{{$test->duration}}'</span>
        </div>
        <div class="spec-wrap col-xs-12 col-sm-6 col-md-3">
          <span class="title">Finish Time</span><br>
          <span class="value">{{Carbon\Carbon::parse($test->scheduled_at)->addMinutes($test->duration)->format('H:i')}}</span>
        </div>
        <hr class="col-xs-12">
        <div class="spec-wrap col-xs-12 col-sm-6 col-md-4">
          <span class="title">Registered Users</span><br>
          <span class="value">{{count($test->users)}}</span>
        </div>
        <div class="spec-wrap col-xs-12 col-sm-6 col-md-4">
          <span class="title">Active Professors</span><br>
          <span class="value">2</span>
        </div>
        <div class="spec-wrap col-xs-12 col-sm-6 col-md-4">
          <span class="title">Active Students</span><br>
          <span class="value">45</span>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/locale/en.js"></script>
  <script>
    let test_name = '{{$test->name}}'
    const announcements = {
      inFarFuture   : ['This test will take place '],
      inFuture      : ['This test will take place '],
      inCloseFuture : ['This test will start '],
      started       : ['This test is currently taking place and will end '],
      finished      : ['This test took place '],
      graded        : ['This test took place ']
    }
    const icons = {
      inFarFuture   : ['<i class="fa fa-calendar" aria-hidden="true"></i>'],
      inFuture      : ['<i class="fa fa-calendar" aria-hidden="true"></i>'],
      inCloseFuture : ['<i class="fa fa-envelope" aria-hidden="true"></i>'],
      started       : ['<i class="fa fa-envelope-open" aria-hidden="true"></i>'],
      finished      : ['<i class="fa fa-calculator" aria-hidden="true"></i>'],
      graded        : ['<i class="fa fa-calculator" aria-hidden="true"></i>']
    }
    //
    let role = '{{Auth::user()->role}}'
    let scheduled_at = '{{$test->scheduled_at}}'
    let duration = parseInt('{{$test->duration}}')*60
    let diff = moment().diff(moment(scheduled_at),'seconds')
    /*setInterval(function(){
      ++diff
      AnnouncementUpdater()
    },1000)*/

    function TimelineGenerator(){
      console.log(diff)
      console.log(diff+duration)
      if(diff < 0){
        if(diff/(24*60*60) > 1)
          return 'inFarFuture'
        else if(diff/(15*60) > 1)
          return 'inFuture'
        else
          return 'inCloseFuture'
      } else if(diff-duration < 0){
        return 'started'
      } else {
        return 'graded'
      }
    }
    function AnnouncementUpdater(){
      timeline = TimelineGenerator()
      console.log(timeline)
      let countdown = timeline == 'graded' ? moment(scheduled_at).fromNow() : (timeline == 'started' ? moment(scheduled_at).add(duration,'seconds').fromNow() : moment(scheduled_at).fromNow())
      $('.announcement .icon').html(icons[timeline])
      $('.announcement .description').html(announcements[timeline][0]+'<b>'+countdown+'</b>.')
      $('.announcement .action-wrap .action').addClass('hidden')
      $('.announcement .action-wrap .action.action-'+timeline).removeClass('hidden')
    }
    /*console.log(moment(scheduled_at).format('dddd'))
    let seconds_left = 59
    let interval = setInterval(function(){
      console.log(seconds_left)
      --seconds_left
      $('#timer').text(seconds_left >= 0 ? seconds_left : 0)
      if(seconds_left < 0)
        stopINterval(interval)
    },1000)*/

    function stopINterval(interval){
      if(interval)
        clearInterval(interval)
    }
  </script>
@endsection