
<div class="panel panel-default task-wrap relative" data-task-type="free_text">
  <div class="order-wrap">
    <div class="order-trigger cursor-pointer" data-order-direction="up"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
    <div class="order-value">@if($fill && $task){{$task->position}}@endif</div>
    <div class="order-trigger cursor-pointer" data-order-direction="down"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
  </div>
  <div class="panel-heading">Free Text Task <span class="trash-btn pull-right"><i class="fa fa-trash"></i></span></div>
  <div class="panel-body">
    <input type="hidden" class="task-id" @if($fill && $task) value="{{$task->id}}" @endif>
    <div class="col-md-10 row-margin-bottom task-title">
      <label>Task Title:</label>
      <textarea type="text" class="form-control default-focus" placeholder="What is Bootstrap?">@if($fill && $task){{$task->description}}@endif</textarea>
    </div>
    <div class="col-md-2 row-margin-bottom task-points">
      <label>Points:</label>
      <input type="number" class="form-control" value="@if($fill && $task){{$task->points}}@endif">
    </div>
    @if($fill && $task)
      @include('includes.form.segments.task_images', ['task'=> $task])
    @endif
    <div class="col-md-12 row-margin-bottom task-free-text">
      <label>Answer Comments:</label>
      <input type="hidden" value="@if($fill && $task){{$task->free_text->id}}@endif">
      <textarea type="text" class="form-control default-focus" placeholder="Bootstrap is a front end library that provides utilities to the developer.">@if($fill && $task){{$task->free_text->description}}@endif</textarea>
    </div>
  </div>
</div>