@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <h1>Create Segment</h1>
        <div class="panel panel-default">
          <div class="panel-heading">Basic Information</div>
          <div class="panel-body">
            <form>
              <div class="col-md-2 btn-row-margin-bottom">
                <label>Lesson:</label>
                <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Select Lesson <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    @foreach($lessons as $lesson)
                      <li @if(Request::input('lesson','') == $lesson->id)class="active"@endif><a href="#">{{$lesson->name}}</a></li>
                    @endforeach
                  </ul>
                </div>
              </div>
              <div class="col-md-8 col-md-offset-2 btn-row-margin-bottom">
                <label>Name:</label>
                  <input type="text" class="form-control" placeholder="Basic HTML questions">
              </div>
              <div class="col-md-12">
                <label>Description:</label>
                <textarea type="text" class="form-control" placeholder="All you need to know about HTML..."></textarea>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="btn-row-margin-bottom col-md-8 col-md-offset-2">
        <div class="row">
          <div class="col-xs-12">
            <div class="btn-group pull-left">
              <button type="button" class="btn btn-default" >
                <i class="fa fa-plus"></i> Single Choice
              </button>
            </div>
            <div class="btn-group btn-margin-left pull-left">
              <button type="button" class="btn btn-default" >
                <i class="fa fa-plus"></i> Multiple Choice
              </button>
            </div>
            <div class="btn-group btn-margin-left pull-right">
              <button type="button" class="btn btn-primary" >
                <i class="fa fa-save"></i> Save
              </button>
            </div>
          </div>
        </div>  
      </div>  
    </div>  
  </div>
@endsection
