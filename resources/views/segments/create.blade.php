@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="btn-row-margin-bottom col-md-8 col-md-offset-2">
        <div class="row">
          <div class="col-xs-8">
            <div class="btn-group pull-left">
              <button type="button" class="btn btn-primary" >
                <i class="fa fa-plus"></i> Single Choice
              </button>
            </div>
            <div class="btn-group btn-margin-left pull-left">
              <button type="button" class="btn btn-primary" >
                <i class="fa fa-plus"></i> Multiple Choice
              </button>
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
          <div class="panel-heading">Create Segment</div>
          <div class="panel-body">
            <form>
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Name">
              </div>
              <div class="input-group">
                <textarea type="text" class="form-control" placeholder="Name"></textarea>
              </div>
            </form>
          </div>
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
