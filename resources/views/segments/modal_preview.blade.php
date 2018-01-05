<div class="col-xs-12">
  <h1>{{$segment->title}}</h1>
  <p>{{$segment->description}}</p>
  @foreach($segment->tasks as $task)
    @include('includes.preview.segments.task_types.'.$task->type, ['task' => $task])
  @endforeach
</div>