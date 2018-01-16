@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="row-margin-bottom col-xs-12">
        <div class="row">
          <div class="col-xs-8">
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
                <li @if(Request::input('lesson','') == '')class="active"@endif><a href="{{route('tests_index',[])}}">All</a></li>
                <li role="separator" class="divider"></li>
                @foreach($lessons as $lesson)
                  <li @if(Request::input('lesson','') == $lesson->id)class="active"@endif>
                    <a href="{{route('tests_index',['lesson'=>$lesson->id])}}">{{$lesson->name}}</a>
                  </li>
                @endforeach
              </ul>
            </div>
            <div class="btn-group margin-left-15 pull-left">
              <a href="{{url('tests/create')}}" type="button" class="btn btn-primary" >
                <i class="fa fa-plus"></i> Create
              </a>
            </div>
          </div>
          <div class="col-xs-4">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Search">
              <span class="input-group-btn">
                <a href="{{route('tests_index',Request::except('page'))}}" class="btn btn-default" type="button"><i class="fa fa-search"></i></a>
              </span>
            </div>
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
              <th>Scheduled for</th>
              <th>Segments</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
            @foreach($tests as $test)
              <tr>
                <td>{{$test->name}}</td>
                <td>{{$test->lesson->name}}</td>
                <td>{{$test->scheduled_at}}</td>
                <td>{{$test->segments_count}}</td>
                <td>{{$test->status}}</td>
                <td>
                  @if(true)
                    <a href="{{url('tests/'.$test->id)}}" type="button" class="btn btn-primary btn-xs">
                      <i class="fa fa-eye"></i>
                    </a>
                  @endif
                  @if(in_array($test->status,['draft','published']))
                    <a href="{{url('tests/'.$test->id.'/edit')}}" type="button" class="btn btn-success btn-xs">
                      <i class="fa fa-pencil"></i>
                    </a>
                  @endif
                  @if($test->status == 'draft')
                    <a href="{{url('tests/'.$test->id.'/delete')}}" type="button" class="btn btn-danger btn-xs">
                      <i class="fa fa-trash"></i>
                    </a>
                  @endif
                  @if($test->status != 'draft')
                    <a href="{{url('tests/'.$test->id.'/lobby')}}" type="button" class="btn btn-primary btn-xs">
                      <i class="fa fa-users"></i>
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
