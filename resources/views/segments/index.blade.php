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
                <li @if(Request::input('lesson','') == '')class="active"@endif><a href="{{route('segments_index',Request::except('page'))}}">All</a></li>
                <li role="separator" class="divider"></li>
                @foreach($lessons as $lesson)
                  <li @if(Request::input('lesson','') == $lesson->id)class="active"@endif>
                    <a href="{{route('segments_index',array_merge(Request::except('page'),['lesson'=>$lesson->id]))}}">{{$lesson->name}}</a>
                  </li>
                @endforeach
              </ul>
            </div>
            <div class="btn-group margin-left-15 pull-left">
              <a href="{{url('segments/create')}}" type="button" class="btn btn-primary" >
                <i class="fa fa-plus"></i> Create
              </a>
            </div>
          </div>
          <div class="col-xs-3">
            <div class="input-group search-wrap">
              <input type="text" class="form-control" placeholder="Search" value="{{Request::input('search','')}}">
              <span class="input-group-btn">
                <a href="#" class="btn btn-default" type="button"><i class="fa fa-search"></i></a>
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
              <th>Tests</th>
              <th>Action</th>
            </tr>
            @foreach($segments as $segment)
              <tr>
                <td>{{$segment->title}}</td>
                <td>{{$segment->lesson->name}}</td>
                <td>{{$segment->tests_count}}</td>
                <td>
                  <a href="{{url('segments/'.$segment->id.'/preview')}}" type="button" class="btn btn-primary btn-xs">
                    <i class="fa fa-eye"></i>
                  </a>
                  <a href="{{url('segments/'.$segment->id.'/edit')}}" type="button" class="btn btn-success btn-xs">
                    <i class="fa fa-pencil"></i>
                  </a>
                  <a href="{{url('segments/'.$segment->id.'/delete')}}" type="button" class="btn btn-danger btn-xs">
                    <i class="fa fa-trash"></i>
                  </a>
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
          {{ $segments->appends(Request::except('page'))->links() }}
        </nav>
      </div>
    </div>
  </div>
@endsection
