@extends('layouts.app')

@section('styles')
  <style type="text/css">
  </style>
@endsection

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-3" id="segment-body">
        <h1>{{$segment->title}}</h1>
        <p>{{$segment->description}}</p>
        @foreach($segment->tasks as $task)
          @include('includes.preview.segments.task_types.'.$task->type, ['task' => $task])
        @endforeach
      </div>
    </div>
    <div class="fixed-toolbar col-xs-2">
      <h5>Actions</h5>
      <div class="btn-group margin-bottom-15">
        <a href="{{url('segments/'.$segment->id.'/edit')}}" class="btn btn-default">
          <i class="fa fa-pencil"></i> Edit
        </a>
      </div>
    </div>
  </div>
@endsection