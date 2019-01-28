<div class="panel panel-default task-wrap relative" data-task-type="free-text">
  <div class="panel-heading">{{$task->description}} <span class="pull-right">{{$task->points}} pts</span></div>
  <div class="panel-body">
    <p>
      {{$task->free_text->description}}
    </p>
  </div>
</div>