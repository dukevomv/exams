@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="btn-row-margin-bottom col-md-8 col-md-offset-2">
        <div class="row">
          <div class="col-xs-8">
            <div class="btn-group pull-left">
              <button id="lesson" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ucfirst(Request::input('lesson','all'))}} <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li @if(Request::input('lesson','') == '')class="active"@endif><a href="{{route('lessons_index',[])}}">All</a></li>
              </ul>
            </div>
            <div class="btn-group btn-margin-left pull-left">
              <a href="{{url('segments/create')}}" type="button" class="btn btn-primary" >
                <i class="fa fa-plus"></i> Create
              </a>
            </div>
          </div>
          <div class="col-xs-4">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Search">
              <span class="input-group-btn">
                <a href="{{route('lessons_index',Request::except('page'))}}" class="btn btn-default" type="button"><i class="fa fa-search"></i></a>
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
              <th>Lesson</th>
              <th>Tests</th>
              <th>Action</th>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <nav class="pull-right" aria-label="Page navigation">
        </nav>
      </div>
    </div>
  </div>
@endsection
