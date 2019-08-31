<div class="panel panel-default task-wrap relative" data-task-type="correspondence">
  <div class="order-wrap">
    <div class="order-trigger cursor-pointer" data-order-direction="up"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
    <div class="order-value">@if($fill && $task){{$task->position}}@endif</div>
    <div class="order-trigger cursor-pointer" data-order-direction="down"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
  </div>
  <div class="panel-heading">Correspondence Task <span class="trash-btn pull-right"><i class="fa fa-trash"></i></span></div>
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
    <div class="task-list">
      @if($fill && $task)
        @foreach($task->correspondence as $choice)
          <div class="col-md-12 task-choice row-margin-bottom">
            <input type="hidden" class="choice-id" value="{{$choice->id}}">
            <div class="input-group col-xs-11 pull-left">
              <input type="text" class="form-control default-focus side-a" placeholder="Choice" value="{{$choice->side_a}}">
              <span class="input-group-addon"><i class="fa fa-hand-o-right"></i></span>
              <input type="text" class="form-control default-focus side-b" placeholder="Matching Pair" value="{{$choice->side_b}}">
            </div>
            <span class="pull-right cursor-pointer trash-choice"><i class="fa fa-minus-circle"></i></span>
          </div>
        @endforeach
      @else
        <div class="col-md-12 task-choice row-margin-bottom">
          <input type="hidden" class="choice-id">
          <div class="input-group col-xs-11 pull-left">
            <input type="text" class="form-control default-focus side-a" placeholder="Choice">
            <span class="input-group-addon"><i class="fa fa-hand-o-right"></i></span>
            <input type="text" class="form-control default-focus side-b" placeholder="Matching Pair">
          </div>
          <span class="pull-right cursor-pointer trash-choice"><i class="fa fa-minus-circle"></i></span>
        </div>
      @endif
    </div>
    <div class="col-md-12">
      <div class="new-choice-wrap hidden">
        <div class="col-md-12 task-choice row-margin-bottom">
          <input type="hidden" class="choice-id">
          <div class="input-group col-xs-11 pull-left">
            <input type="text" class="form-control default-focus side-a" placeholder="Choice">
            <span class="input-group-addon"><i class="fa fa-hand-o-right"></i></span>
            <input type="text" class="form-control default-focus side-b" placeholder="Matching Pair">
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