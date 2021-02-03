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
                {{$selected_lesson}} <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li @if(Request::input('lesson','') == '')class="active"@endif><a href="{{route('tests_index',Request::except('page'))}}">All</a></li>
                <li role="separator" class="divider"></li>
                @foreach($lessons as $lesson)
                  <li @if(Request::input('lesson','') == $lesson->id)class="active"@endif>
                    <a href="{{route('tests_index',array_merge(Request::except('page'),['lesson'=>$lesson->id]))}}">{{$lesson->name}}</a>
                  </li>
                @endforeach
              </ul>
            </div>
            @if (Auth::user()->role == 'professor')
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
              <th>Lesson</th>
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

                  if(Auth::user()->role == \App\Enums\UserRole::STUDENT && $testUser && $testUser->status == \App\Enums\TestUserStatus::GRADED){
                      $grade = \App\Util\Points::getWithPercentage($testUser->given_points,$testUser->total_points);
                  }
                //todo make this to calculate test user grades if graded
                @endphp
                <td>{{$grade}}</td>
                <td class="text-center">
                  @if(true)
                    <a href="{{url('tests/'.$test->id)}}" type="button" class="btn btn-primary btn-xs">
                      <i class="fa fa-eye"></i>
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
