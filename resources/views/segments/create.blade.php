@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-3" id="segment-body">
        <h1>Create Segment</h1>
        <div class="panel panel-default relative">
          <div class="order-wrap disabled">
            <div class="order-trigger cursor-pointer" data-order-direction="up"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
            <div class="order-value">0</div>
            <div class="order-trigger cursor-pointer" data-order-direction="down"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
          </div>
          <div class="panel-heading">Basic Information</div>
          <div class="panel-body">
            <div class="col-md-2 row-margin-bottom">
              <label>Lesson:</label>
              <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Select Lesson <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  @foreach($lessons as $lesson)
                    <li @if(Request::input('lesson','') == $lesson->id)class="active"@endif><a href="#">{{$lesson->name}}</a></li>
                  @endforeach
                </ul>
              </div>
            </div>
            <div class="col-md-8 col-md-offset-2 row-margin-bottom">
              <label>Name:</label>
              <input type="text" class="form-control" placeholder="Basic HTML questions">
            </div>
            <div class="col-md-12">
              <label>Description:</label>
              <textarea type="text" class="form-control" placeholder="All you need to know about HTML..."></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="fixed-toolbar col-xs-2">
      <h5>Tools</h5>
      <div class="btn-group margin-bottom-15">
        <button type="button" class="btn btn-default task-type" data-task-type="rmc">
          <i class="fa fa-plus"></i> Single Choice
        </button>
      </div>
      <div class="btn-group margin-bottom-15">
        <button type="button" class="btn btn-default task-type" data-task-type="cmc">
          <i class="fa fa-plus"></i> Multiple Choice
        </button>
      </div>
      <div class="hidden" id="tool-dom">
        <div class="task-dom" id="rmc-dom">
          <div class="panel panel-default task-wrap relative">
            <div class="order-wrap">
              <div class="order-trigger cursor-pointer" data-order-direction="up"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
              <div class="order-value"></div>
              <div class="order-trigger cursor-pointer" data-order-direction="down"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
            </div>
            <div class="panel-heading">Single Choice Task <span class="trash-btn pull-right"><i class="fa fa-trash"></i></span></div>
            <div class="panel-body">
              <div class="col-md-12 row-margin-bottom">
                <label>Task Title:</label>
                <textarea type="text" class="form-control" placeholder="What is Bootstrap?"></textarea>
              </div>
              <div class="col-md-12 task-answer row-margin-bottom">
                <div class="input-group">
                  <span class="input-group-addon cursor-pointer"><i class="fa fa-arrows" aria-hidden="true"></i></span>
                  <input type="text" name="answers[0]" class="form-control" placeholder="Choice 1">
                  <span class="input-group-addon">
                    <label class="cursor-pointer"><input type="radio" name="correct" checked> Correct</label>
                  </span>
                </div>
              </div>
              <div class="col-md-12 task-answer row-margin-bottom">
                <div class="input-group">
                  <span class="input-group-addon cursor-pointer"><i class="fa fa-arrows" aria-hidden="true"></i></span>
                  <input type="text" name="answers[0]" class="form-control" placeholder="Choice 2">
                  <span class="input-group-addon">
                    <label class="cursor-pointer"><input type="radio" name="correct"> Correct</label>
                  </span>
                </div>
              </div>
              <div class="col-md-12">
                <button type="button" class="btn btn-link" >
                  <i class="fa fa-plus"></i> Add Choice
                </button>
              </div>
            </div>
          </div> 
        </div>
        <div class="task-dom" id="cmc-dom">
          <div class="panel panel-default task-wrap relative">
            <div class="order-wrap">
              <div class="order-trigger cursor-pointer" data-order-direction="up"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
              <div class="order-value"></div>
              <div class="order-trigger cursor-pointer" data-order-direction="down"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
            </div>
            <div class="panel-heading">Multiple Choice Task <span class="trash-btn pull-right"><i class="fa fa-trash"></i></span></div>
            <div class="panel-body">
              <div class="col-md-12 row-margin-bottom">
                <label>Task Title:</label>
                <textarea type="text" class="form-control" placeholder="What is Bootstrap?"></textarea>
              </div>
              <div class="col-md-12 task-answer row-margin-bottom">
                <div class="input-group">
                  <span class="input-group-addon cursor-pointer"><i class="fa fa-arrows" aria-hidden="true"></i></span>
                  <input type="text" name="answers[0]" class="form-control" placeholder="Choice 1">
                  <span class="input-group-addon">
                    <label class="cursor-pointer"><input type="checkbox" name="correct" checked> Correct</label>
                  </span>
                </div>
              </div>
              <div class="col-md-12 task-answer row-margin-bottom">
                <div class="input-group">
                  <span class="input-group-addon cursor-pointer"><i class="fa fa-arrows" aria-hidden="true"></i></span>
                  <input type="text" name="answers[0]" class="form-control" placeholder="Choice 2">
                  <span class="input-group-addon">
                    <label class="cursor-pointer"><input type="checkbox" name="correct"> Correct</label>
                  </span>
                </div>
              </div>
              <div class="col-md-12">
                <button type="button" class="btn btn-link" >
                  <i class="fa fa-plus"></i> Add Choice
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <h5>Actions</h5>
      <div class="btn-group margin-bottom-15">
        <button type="button" class="btn btn-default">
          <i class="fa fa-eye"></i> Preview
        </button>
      </div>
      <div class="btn-group margin-bottom-15">
        <button type="button" class="btn btn-primary">
          <i class="fa fa-save"></i> Save
        </button>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script type="text/javascript">

    $('.fixed-toolbar button.task-type').on('click',function(e){
      $('#segment-body').append($('#tool-dom #'+$(this).attr('data-task-type')+'-dom').html())
      ReorderSegmentTasks()
    })

    $(document).on('click','#segment-body .task-wrap .order-wrap .order-trigger',function(e){
      let direction = $(this).attr('data-order-direction')
      let current_task = $(this).closest('.task-wrap')
      let target_task = null
      if(direction == 'up')
        target_task = current_task.prev('.task-wrap')
      else if(direction == 'down')
        target_task = current_task.next('.task-wrap')

      if(target_task.length > 0){
        current_task.detach()
        if(direction == 'up')
          current_task.insertBefore(target_task)
        else if(direction == 'down')
          current_task.insertAfter(target_task)
        ReorderSegmentTasks()
      }
    })

    $(document).on('click','.task-wrap .trash-btn',function(e){
      $(this).closest('.task-wrap').remove()
      ReorderSegmentTasks()
    })

    function ReorderSegmentTasks(){
      $("#segment-body .task-wrap").each(function(index) {
        $(this).find('.order-wrap .order-value').text(index+1)
      });
    }
  </script>
@endsection
