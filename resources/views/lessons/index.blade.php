@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="row-margin-bottom col-xs-12">
        <div class="row">
          <div class="col-xs-9">
            <div class="btn-group pull-left">
              <button id="status" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ucfirst(Request::input('status','all'))}} <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li @if(Request::input('status','') == '')class="active"@endif><a href="{{route('lessons_index',Request::except('page'))}}">All</a></li>
                <li @if(Request::input('status','') == 'approved')class="active"@endif><a href="{{route('lessons_index',array_merge(Request::except('page'),['status'=>'approved']))}}">Approved</a></li>
                <li @if(Request::input('status','') == 'pending')class="active"@endif><a href="{{route('lessons_index',array_merge(Request::except('page'),['status'=>'pending']))}}">Pending</a></li>
                <li @if(Request::input('status','') == 'unsubscribed')class="active"@endif><a href="{{route('lessons_index',array_merge(Request::except('page'),['status'=>'unsubscribed']))}}">Unsubscribed</a></li>
              </ul>
            </div>
            <div class="btn-group margin-left-15 pull-left">
              <a href="{{url('lessons/create')}}" type="button" class="btn btn-primary" >
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
              <th>Semester</th>
              <th>Code</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
            @foreach($lessons as $lesson)
              <tr>
                <td>{{$lesson->name}}</td>
                <td>{{$lesson->semester}}</td>
                <td>{{$lesson->gunet_code}}</td>
                <td>
                  @if(is_null($lesson->status))
                    Unsubscribed
                  @elseif($lesson->status->approved == 0)
                    Pending
                  @elseif($lesson->status->approved == 1)
                    Approved
                  @endif
                </td>
                <td>
                  @if(is_null($lesson->status))
                    <button type="button" class="btn btn-success btn-xs">Subscribe</button>
                  @elseif($lesson->status->approved == 0)
                    <button type="button" class="btn btn-danger btn-xs">Cancel</button>
                  @elseif($lesson->status->approved == 1)
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
          {{ $lessons->appends(Request::except('page'))->links() }}
        </nav>
      </div>
    </div>
  </div>
@endsection
