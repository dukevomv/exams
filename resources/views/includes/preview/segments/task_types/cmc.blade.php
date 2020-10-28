<div class="panel panel-default task-wrap relative" data-task-type="cmc" data-task-id="{{$task->id}}">
  <div class="panel-heading">{{$task->description}} <span class="pull-right">{{$task->points}} pts</span></div>
  <div class="panel-body">
    <div class="task-list">
      @foreach($task->cmc as $choice)
        <div class="col-md-12 task-choice row-margin-bottom">
          <label class="cursor-pointer">
            <span class="task-value" data-value="{{$choice->id}}" data-key="id"></span>
            <input type="checkbox" class="task-value" data-key="correct" data-value-prop="checked" id="task-{{$task->id}}-choice-{{$choice->id}}" @if($choice->selected) checked @endif class="autosave-field choice-correct set-unique-val">
            <span>{{$choice->description}}</span>
          </label>
          @if($choice->selected)
            @include('includes.preview.segments.task_points', ['value' =>round($task->points/count($task->cmc),2),'correct' => ($choice->correct == 1)])
          @endif
        </div>
      @endforeach
    </div>
  </div>
</div>