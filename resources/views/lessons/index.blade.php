@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="button-row col-md-8 col-md-offset-2">
        <div class="row">
          <div class="col-xs-8">
            <div class="btn-group pull-left">
              <button id="status" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ucfirst(app('request')->input('status','all'))}} <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li @if(app('request')->input('status','') == '')class="active"@endif><a href="{{route('lessons_index',['page'=>1])}}">All</a></li>
                <li @if(app('request')->input('status','') == 'approved')class="active"@endif><a href="{{route('lessons_index',['status'=>'approved','page'=>1])}}">Approved</a></li>
                <li @if(app('request')->input('status','') == 'pending')class="active"@endif><a href="{{route('lessons_index',['status'=>'pending','page'=>1])}}">Pending</a></li>
                <li @if(app('request')->input('status','') == 'unsubscribed')class="active"@endif><a href="{{route('lessons_index',['status'=>'unsubscribed','page'=>1])}}">Unsubscribed</a></li>
              </ul>
            </div>
          </div>
          <div class="col-xs-4">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Search">
              <span class="input-group-btn">
                <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </div>
        </div>  
      </div>  
    </div>  
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
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
      <div class="col-md-8 col-md-offset-2">
        <nav class="pull-right" aria-label="Page navigation">
          {{ $lessons->appends(['status' => app('request')->input('status','all')])->links() }}
        </nav>
      </div>
    </div>
  </div>
@endsection
