<div class="panel panel-default task-wrap task-wrap-{{$task->type}} relative" id="panel-task-{{$task->id}}"
     data-task-type="{{$task->type}}" data-task-id="{{$task->id}}">
    <div class="panel-heading">
        {{$task->description}}
        @include('includes.preview.segments.task_points', ['given' =>$task->given_points,'total' => $task->points])
    </div>
    <div class="panel-body">
        @include('includes.preview.segments.task_types.'.$task->type, ['task' => $task])
    </div>
</div>