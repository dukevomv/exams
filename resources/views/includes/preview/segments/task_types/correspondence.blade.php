<div class="panel panel-default task-wrap relative" data-task-type="correspondence" data-task-id="{{$task->id}}">
  <?php
    $sides = ['a'=>[],'b'=>[]];
  ?>
  @foreach($task->correspondence as $cor)
  <?php
    $sides['a'][] = $cor->side_a;
    $sides['b'][] = $cor->side_b;
  ?>
  @endforeach
  <?php shuffle($sides['b']);shuffle($sides['a']); ?>
  <div class="panel-heading">{{$task->description}} <br><br>
    @foreach($sides['b'] as $b)
      <a href="#" class="btn btn-info">
        {{$b}}
      </a>
    @endforeach
    <span class="pull-right">{{$task->points}} pts</span>
  </div>
  <div class="panel-body">
    @foreach($sides['a'] as $a)
      <div class="col-xs-8 row-margin-bottom">
        <a href="#" class="btn btn-default col-xs-8">
          {{$a}}
        </a>
      </div>
      <div class="col-xs-4 row-margin-bottom">
        <a href="#" class="btn btn-default btn-dotted">Drag here</a>
      </div>
    @endforeach
  </div>
</div>