<?php $rmc_name = uniqid('uniq_');?>
<div class="panel panel-default task-wrap relative" data-task-type="rmc">
  <div class="panel-heading">{{$task->description}} <span class="pull-right">{{$task->points}} pts</span></div>
  <div class="panel-body">
    <div class="task-list">
      @foreach($task->rmc as $choice)
        <div class="col-md-12 task-choice row-margin-bottom">
          <label class="cursor-pointer">
            <input type="radio" class="choice-correct set-unique-val" name="{{$rmc_name}}"> 
            <span>{{$choice->description}}</span>
          </label>
        </div>
      @endforeach
    </div>
  </div>
</div>