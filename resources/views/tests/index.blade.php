@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="row-margin-bottom col-xs-12">
        <div class="row">
          <div class="col-xs-9">
            <div class="btn-group pull-left">
              <button id="lesson" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php
                $selected_lesson = Request::input('lesson','All');
                foreach($lessons as $lesson){
                  if($selected_lesson == $lesson->id){
                    $selected_lesson = $lesson->name;
                    break;
                  }
                }
                ?>
                Course: {{$selected_lesson}} <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li @if(Request::input('lesson','') == '')class="active"@endif><a href="{{route('tests_index',Request::except('page','lesson'))}}">All</a></li>
                <li role="separator" class="divider"></li>
                @foreach($lessons as $lesson)
                  <li @if(Request::input('lesson','') == $lesson->id)class="active"@endif>
                    <a href="{{route('tests_index',array_merge(Request::except('page'),['lesson'=>$lesson->id]))}}">{{$lesson->name}}</a>
                  </li>
                @endforeach
              </ul>
            </div>
            <div class="btn-group pull-left margin-left-15">
              <button id="lesson" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php
                $selected_status = Request::input('status','All');
                foreach(\App\Enums\TestStatus::values() as $status){
                  if($selected_status == $status){
                    $selected_status = ucFirst($status);
                    break;
                  }
                }
                ?>
                Status: {{$selected_status}} <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li @if(Request::input('status','') == '')class="active"@endif><a href="{{route('tests_index',Request::except('page','status'))}}">All</a></li>
                <li role="separator" class="divider"></li>
                @foreach(\App\Enums\TestStatus::values() as $status)
                  <li @if(Request::input('status','') == $status)class="active"@endif>
                    <a href="{{route('tests_index',array_merge(Request::except('page'),['status'=>$status]))}}">{{ucfirst($status)}}</a>
                  </li>
                @endforeach
              </ul>
            </div>
            @if (Auth::user()->can('createTests'))
              <div class="btn-group margin-left-15 pull-left">
                <a href="{{url('tests/create')}}" type="button" class="btn btn-primary" >
                  <i class="fa fa-plus"></i> Create
                </a>
              </div>
            @endif
          </div>
          <div class="col-xs-3">
            @include('includes.assets.search-wrap', ['value'=>Request::input('search','')])
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <div class="panel panel-default">
          <table class="table">
            <tr>
              <th>Name</th>
              <th>Course</th>
              <th>Duration</th>
              <th>Scheduled at</th>
              <th>Status</th>
              <th>Grade</th>
              <th class="text-center">Action</th>
            </tr>
            @foreach($tests as $test)
              <tr>
                <td>{{$test->name}}</td>
                <td>{{$test->lesson->name}}</td>
                <td>@if(!is_null($test->duration)){{$test->duration}}'@endif</td>
                <td>{{ !is_null($test->scheduled_at) ? $test->scheduled_at->format('d M Y, H:i') : '-'}}</td>
                <td>{{ucfirst($test->status)}}</td>
                @php
                  $grade = '-';
                  $testUser = is_null($test->user_on_test) ? null : $test->user_on_test->pivot;

                  if(Auth::user()->role == \App\Enums\UserRole::STUDENT && $test->status == \App\Enums\TestStatus::GRADED && $testUser && $testUser->status == \App\Enums\TestUserStatus::GRADED){
                        $grade = \App\Util\Points::getWithPercentage($testUser->given_points,$testUser->total_points);
                  }
                  if(Auth::user()->role == \App\Enums\UserRole::PROFESSOR && isset($test->stats) && $test->stats['students']['graded'] > 0){
                        $grade = 'avg: '.$test->stats['average'];
                  }
                @endphp
                <td>{{$grade}}</td>
                <td class="text-center">
                  @if(true)
                    <a href="{{url('tests/'.$test->id)}}" type="button" class="btn btn-primary btn-xs">
                      <i class="fa fa-eye"></i>
                    </a>
                  @endif
                  @if(in_array($test->status,['published']) && Auth::user()->role == 'professor')
                    <a href="{{url('tests/'.$test->id.'/invited-students')}}" type="button" class="btn btn-info btn-xs">
                      <i class="fa fa-envelope"></i>
                    </a>
                  @endif
                  @if(in_array($test->status,['draft','published']) && Auth::user()->role == 'professor')
                    <a href="{{url('tests/'.$test->id.'/edit')}}" type="button" class="btn btn-success btn-xs">
                      <i class="fa fa-pencil"></i>
                    </a>
                  @endif
                  @if($test->status == 'draft')
                    <a href="{{url('tests/'.$test->id.'/delete')}}" type="button" class="btn btn-danger btn-xs">
                      <i class="fa fa-trash"></i>
                    </a>
                  @endif
                </td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <nav class="pull-right" aria-label="Page navigation">
          {{ $tests->appends(Request::except('page'))->links() }}
        </nav>
      </div>
    </div>
  </div>
@endsection
