@extends('layouts.app')

@section('styles')
  <style type="text/css">
  .btn-dotted{
    border-style: dashed;
    color:#ccc;
  }
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
    <div class="fixed-toolbar col-xs-2 hidden">
      <h5>Actions</h5>
      <div class="btn-group margin-bottom-15">
        <a href="{{url('segments/'.$segment->id.'/edit')}}" class="btn btn-default">
          <i class="fa fa-pencil"></i> Edit
        </a>
      </div>
    </div>
  </div>
@endsection


@section('scripts')
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>
  <script type="text/javascript">

  </script>
@endsection