<div class="panel panel-default task-wrap relative" data-task-type="cmc">
  <div class="order-wrap">
    <div class="order-trigger cursor-pointer" data-order-direction="up"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
    <div class="order-value"></div>
    <div class="order-trigger cursor-pointer" data-order-direction="down"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
  </div>
  <div class="panel-heading">Multiple Choice Task <span class="trash-btn pull-right"><i class="fa fa-trash"></i></span></div>
  <div class="panel-body">
    <div class="col-md-10 row-margin-bottom task-title">
      <label>Task Title:</label>
      <textarea type="text" class="form-control default-focus" placeholder="What is Bootstrap?">@if($fill && $task){{$task->description}}@endif</textarea>
    </div>
    <div class="col-md-2 row-margin-bottom task-points">
      <label>Points:</label>
      <input type="number" class="form-control" value="@if($fill && $task){{$task->points}}@endif">
    </div>
    <div class="task-list">
      @if($fill && $task)
        @foreach($task->cmc as $choice)
          <div class="col-md-12 task-choice row-margin-bottom">
            <div class="input-group col-xs-11 pull-left">
              <span class="input-group-addon cursor-pointer choice-handle"><i class="fa fa-arrows"></i></span>
              <input type="text" class="form-control task-desc" placeholder="Choice" value="{{$choice->description}}">
              <span class="input-group-addon">
                <label class="cursor-pointer"><input type="checkbox" class="task-correct" name="correct" @if($choice->correct) checked @endif> Correct</label>
              </span>
            </div>
            <span class="pull-right cursor-pointer trash-choice"><i class="fa fa-minus-circle"></i></span>
          </div>
        @endforeach
      @else
        <div class="col-md-12 task-choice row-margin-bottom">
          <div class="input-group col-xs-11 pull-left">
            <span class="input-group-addon cursor-pointer choice-handle"><i class="fa fa-arrows"></i></span>
            <input type="text" class="form-control default-focus task-desc" placeholder="Choice">
            <span class="input-group-addon">
              <label class="cursor-pointer"><input type="checkbox" class="task-correct" name="correct"> Correct</label>
            </span>
          </div>
          <span class="pull-right cursor-pointer trash-choice"><i class="fa fa-minus-circle"></i></span>
        </div>
      @endif
    </div>
    <div class="col-md-12">
      <div class="new-choice-wrap hidden">
        <div class="col-md-12 task-choice row-margin-bottom">
          <div class="input-group col-xs-11 pull-left">
            <span class="input-group-addon cursor-pointer choice-handle"><i class="fa fa-arrows"></i></span>
            <input type="text" class="form-control task-desc default-focus" placeholder="Choice">
            <span class="input-group-addon">
              <label class="cursor-pointer"><input type="checkbox" class="task-correct" name="correct"> Correct</label>
            </span>
          </div>
          <span class="pull-right cursor-pointer trash-choice"><i class="fa fa-minus-circle"></i></span>
        </div>
      </div>
      <button type="button" class="btn btn-link add-choice">
        <i class="fa fa-plus"></i> Add Choice
      </button>
    </div>
  </div>
</div>