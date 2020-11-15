<style type="text/css">
  textarea{
    width:100%;
    border:0;
  }
</style>

<div class="panel panel-default task-wrap relative" data-task-type="free_text" data-task-id="{{$task->id}}">
  <div class="panel-heading">{{$task->description}} @include('includes.preview.segments.task_points', ['total' => $task->points])</div>
  <div class="panel-body">
    <textarea class="col-md-12 task-value" data-key="correct" data-value-prop="textarea" data-input-label="answer" rows="7">{{$task->answer}}</textarea>
  </div>
</div>