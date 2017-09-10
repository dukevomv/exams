@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-3">
        <h1>Create Segment</h1>
        <div class="panel panel-default relative">
          <div class="order-wrap disabled">
            <div class="order-up cursor-pointer"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
            <div class="order-value">0</div>
            <div class="order-down cursor-pointer"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
          </div>
          <div class="panel-heading">Basic Information</div>
          <div class="panel-body">
            <div class="col-md-2 row-margin-bottom">
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
            <div class="col-md-8 col-md-offset-2 row-margin-bottom">
              <label>Name:</label>
              <input type="text" class="form-control" placeholder="Basic HTML questions">
            </div>
            <div class="col-md-12">
              <label>Description:</label>
              <textarea type="text" class="form-control" placeholder="All you need to know about HTML..."></textarea>
            </div>
          </div>
        </div>
        <div class="panel panel-default relative">
          <div class="order-wrap">
            <div class="order-up cursor-pointer"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
            <div class="order-value">1</div>
            <div class="order-down cursor-pointer"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
          </div>
          <div class="panel-heading">Single Choice Task</div>
          <div class="panel-body">
            <div class="col-md-12 row-margin-bottom">
              <label>Task Title:</label>
              <textarea type="text" class="form-control" placeholder="What is Bootstrap?"></textarea>
            </div>
            <div class="col-md-12 task-answer row-margin-bottom">
              <div class="input-group">
                <span class="input-group-addon cursor-pointer"><i class="fa fa-arrows" aria-hidden="true"></i></span>
                <input type="text" name="answers[0]" class="form-control" placeholder="Choice 1">
                <span class="input-group-addon">
                  <label class="cursor-pointer"><input type="radio" name="correct" checked> Correct</label>
                </span>
              </div>
            </div>
            <div class="col-md-12 task-answer row-margin-bottom">
              <div class="input-group">
                <span class="input-group-addon cursor-pointer"><i class="fa fa-arrows" aria-hidden="true"></i></span>
                <input type="text" name="answers[0]" class="form-control" placeholder="Choice 2">
                <span class="input-group-addon">
                  <label class="cursor-pointer"><input type="radio" name="correct"> Correct</label>
                </span>
              </div>
            </div>
            <div class="col-md-12">
              <button type="button" class="btn btn-link" >
                <i class="fa fa-plus"></i> Add Choice
              </button>
            </div>
          </div>
        </div>
        <div class="panel panel-default relative">
          <div class="order-wrap">
            <div class="order-up cursor-pointer"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
            <div class="order-value">2</div>
            <div class="order-down cursor-pointer"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
          </div>
          <div class="panel-heading">Multiple Choice Task</div>
          <div class="panel-body">
            <div class="col-md-12 row-margin-bottom">
              <label>Task Title:</label>
              <textarea type="text" class="form-control" placeholder="What is Bootstrap?"></textarea>
            </div>
            <div class="col-md-12 task-answer row-margin-bottom">
              <div class="input-group">
                <span class="input-group-addon cursor-pointer"><i class="fa fa-arrows" aria-hidden="true"></i></span>
                <input type="text" name="answers[0]" class="form-control" placeholder="Choice 1">
                <span class="input-group-addon">
                  <label class="cursor-pointer"><input type="checkbox" name="correct" checked> Correct</label>
                </span>
              </div>
            </div>
            <div class="col-md-12 task-answer row-margin-bottom">
              <div class="input-group">
                <span class="input-group-addon cursor-pointer"><i class="fa fa-arrows" aria-hidden="true"></i></span>
                <input type="text" name="answers[0]" class="form-control" placeholder="Choice 2">
                <span class="input-group-addon">
                  <label class="cursor-pointer"><input type="checkbox" name="correct"> Correct</label>
                </span>
              </div>
            </div>
            <div class="col-md-12">
              <button type="button" class="btn btn-link" >
                <i class="fa fa-plus"></i> Add Choice
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="fixed-toolbar col-xs-2">
      <h5>Tools</h5>
      <div class="btn-group margin-bottom-15">
        <button type="button" class="btn btn-default">
          <i class="fa fa-plus"></i> Single Choice
        </button>
      </div>
      <div class="btn-group margin-bottom-15">
        <button type="button" class="btn btn-default">
          <i class="fa fa-plus"></i> Multiple Choice
        </button>
      </div>
      <h5>Actions</h5>
      <div class="btn-group margin-bottom-15">
        <button type="button" class="btn btn-default">
          <i class="fa fa-eye"></i> Preview
        </button>
      </div>
      <div class="btn-group margin-bottom-15">
        <button type="button" class="btn btn-primary">
          <i class="fa fa-save"></i> Save
        </button>
      </div>
    </div>
  </div>
@endsection
