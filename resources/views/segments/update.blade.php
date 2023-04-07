@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <h1>@if($segment) Edit @else Create @endif Segment    <button class="btn btn-default btn-sm pull-right" data-toggle="modal" data-target="#point-system-modal"><i class="fa fa-info"></i> Point System Guide</button></h1>
      </div>
      <div class="col-md-3 sidebar-toolbar">
        <h5>Actions</h5>
        <div class="btn-group margin-bottom-15">
          <a @if($segment) href="{{url('segments/'.$segment->id.'/preview')}}" @else disabled @endif type="button" class="btn btn-default">
            <i class="fa fa-eye"></i> Preview
          </a>
        </div>
        <div class="btn-group margin-bottom-15">
          <button type="button" id="save-btn" class="btn btn-primary">
            <i class="fa fa-save"></i> Save
          </button>
        </div>


        <h5>Question Types</h5>
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
        <div class="btn-group margin-bottom-15">
          <button type="button" class="btn btn-default task-type" data-task-type="free_text">
            <i class="fa fa-plus"></i> Free Text
          </button>
        </div>
        <div class="btn-group margin-bottom-15">
          <button type="button" class="btn btn-default task-type" data-task-type="correspondence">
            <i class="fa fa-plus"></i> Correspondence
          </button>
        </div>
        <!--<div class="btn-group margin-bottom-15">-->
        <!--  <button type="button" class="btn btn-default task-type" data-task-type="code">-->
        <!--    <i class="fa fa-plus"></i> Code-->
        <!--  </button>-->
        <!--</div>-->
        <div class="hidden" id="tool-dom">
          <div class="task-dom" id="rmc-dom">
            @include('includes.form.segments.task_types.rmc',['fill'=>false])
          </div>
          <div class="task-dom" id="cmc-dom">
            @include('includes.form.segments.task_types.cmc',['fill'=>false])
          </div>
          <div class="task-dom" id="free_text-dom">
            @include('includes.form.segments.task_types.free_text',['fill'=>false])
          </div>
          <div class="task-dom" id="correspondence-dom">
            @include('includes.form.segments.task_types.correspondence',['fill'=>false])
          </div>
          <div class="task-dom" id="code-dom">
            @include('includes.form.segments.task_types.code',['fill'=>false])
          </div>
        </div>
      </div>
      <div class="col-md-9" id="segment-body">
        <div class="panel panel-default basics-wrap relative">
          <div class="order-wrap disabled">
            <div class="order-trigger cursor-pointer" data-order-direction="up"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
            <div class="order-value">0</div>
            <div class="order-trigger cursor-pointer" data-order-direction="down"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
          </div>
          <div class="panel-heading">Basic Information</div>
          <div class="panel-body">
            <div class="col-md-2 row-margin-bottom">
              <input type="hidden" id="segment-id" @if($segment) value="{{$segment->id}}" @endif>
              <label>Course:</label>
              <div class="btn-group dropdown-custom">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  @if($segment) {{$lessons->where('id', $segment->lesson_id)->first()->name}} @else Select Course @endif <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <li class="@if(!$segment) active @endif dropdown-value-default"><a href="#" data-dropdown-value="default">Select Course</a></li>
                  @foreach($lessons as $lesson)
                    <li @if($segment && $segment->lesson_id == $lesson->id) class="active" @endif><a href="#" data-dropdown-value="{{$lesson->id}}">{{$lesson->name}}</a></li>
                  @endforeach
                </ul>
                <select class="hidden dropdown-select" id="segment-lesson">
                  <option value="default">Select Course</option>
                  @foreach($lessons as $lesson)
                    <option value="{{$lesson->id}}"  @if($segment && $segment->lesson_id == $lesson->id) selected @endif>{{$lesson->name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-8 col-md-offset-2 row-margin-bottom">
              <label>Name:</label>
              <input type="text" class="form-control" @if($segment) value="{{$segment->title}}" @endif id="segment-name" placeholder="Basic HTML questions">
            </div>
            <div class="col-md-12">
              <label>Description:</label>
              <textarea type="text" class="form-control" id="segment-description" placeholder="All you need to know about HTML...">@if($segment){{$segment->description}}@endif</textarea>
            </div>
          </div>
        </div>
        @if($segment)
          @foreach($segment->tasks as $task)
            @include('includes.form.segments.task_types.'.$task->type, ['fill'=>true,'task' => $task])
          @endforeach
        @endif
      </div>
    </div>
  </div>
  @include('includes.preview.segments.image_preview_modal')
  @include('includes.preview.segments.point_system_guide_modal')
@endsection

@section('scripts')
  <script type="text/javascript">

    //this is duplicated
    $('#image-modal').on('show.bs.modal', function (event) {
      let button = $(event.relatedTarget)
      let modal = $(this)
      modal.find('.modal-title').text(button.data('title'))
      modal.find('.modal-body img').attr('src',button.data('src'))
    })

    $('.sidebar-toolbar button.task-type').on('click',function(e){
      let new_task = $('#tool-dom #'+$(this).attr('data-task-type')+'-dom')
      SetUniqueValue(new_task)
      let new_task_html = new_task.html()
      $('#segment-body').append(new_task_html)
      FocusTask($('#segment-body .task-wrap').last())
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

    $(document).on('click','.task-wrap .add-choice',function(e){
      let new_choice = $(this).parent().find('.new-choice-wrap').html()
      let panel_body = $(this).closest('.panel-body')
      panel_body.find('.task-list').append(new_choice)
      FocusTask(panel_body.find('.task-list').last())
    })

    $(document).on('click','.task-choice .trash-choice',function(e){
      $(this).closest('.task-choice').remove()
    })

    function ReorderSegmentTasks(){
      $("#segment-body .task-wrap").each(function(index) {
        $(this).find('.order-wrap .order-value').text(index+1)
      })
      $(document).find(".task-wrap .panel-body .task-list").sortable({
        appendTo: document.body,
        cursor: "move",
        items: "> .task-choice",
        placeholder: "choice-placeholder",
        opacity: 0.5,
        handle: '.choice-handle',
        update: function( event, ui ) {}
      });
    }

    function FocusTask(element){
      element.find('.default-focus')
    }

    function SetUniqueValue(task){
      let unique = 'uniq_'+(new Date()).getTime()
      task.find('.set-unique-val').prop('name',unique)
    }

    $("#save-btn").on('click',function(e){
      let thisBtn = $(this)
      thisBtn.addClass('disabled')
      thisBtn.prop('disabled',true)
      let segment = new Segment()
      segment.UpdateTasks()
      $.ajax({
        type: "POST",
        url: "{{url('segments/update')}}",
        data: {_token:"{{csrf_token()}}",...segment},
        success: function(data){
          thisBtn.removeClass('disabled')
          document.location = '/segments/'+data.id+'/edit';
        },
        error: function(data){
          showValidatorErrors(data)
          thisBtn.removeClass('disabled')
          thisBtn.prop('disabled',false)
        },
      })
    })

    var Segment = function Segment(){
      this.constructor
      this.id          = $('.basics-wrap #segment-id').val().trim() != '' ? $('.basics-wrap #segment-id').val().trim() : null
      this.lesson_id   = $('.basics-wrap #segment-lesson').val() != 'default' ? parseInt($('.basics-wrap #segment-lesson').val()) : null
      this.title       = $('.basics-wrap #segment-name').val().trim()
      this.description = $('.basics-wrap #segment-description').val().trim()
      this.tasks       = []
    }

    Segment.prototype.UpdateBasics = function(){
      return false
    }
    Segment.prototype.UpdateTasks = function(){
      let tasks = []
      $("#segment-body .task-wrap").each(function(index) {
        let task_type = $(this).attr('data-task-type')
        tasks.push(GetTaskDetails($(this),task_type))
      })
      this.tasks = tasks

      function GetTaskDetails(element, task_type){
        let task = {
          id          : element.find('.panel-body input.task-id').val().trim() != '' ? element.find('.panel-body input.task-id').val().trim() : null,
          position    : element.find('.order-wrap .order-value').text(),
          type        : task_type,
          description : element.find('.panel-body .task-title textarea').val(),
          points      : element.find('.panel-body .task-points input').val() != '' ? element.find('.panel-body .task-points input').val().trim() : 0,
        }
        task.data = []
        switch(task_type) {
          case "rmc":
          case "cmc":
            element.find('.task-list .task-choice').each(function(i) {
              let choice = {
                id            : $(this).find('input.choice-id').val().trim() != '' ? $(this).find('input.choice-id').val().trim() : null,
                description   : $(this).find('input.choice-desc').val(),
                correct       : $(this).find('input.choice-correct').is(":checked") ? 1 : 0
              }
              if(choice.description != '')   task.data.push(choice)
            })
            break;
          case "free_text":
            task.data.push({
              id          : element.find('.task-free-text input').val(),
              description : element.find('.task-free-text textarea').val(),
              autocomplete: element.find('.task-free-text input.autocomplete').is(":checked") ? 1 : 0
            })
            break;
          case "correspondence":
            element.find('.task-list .task-choice').each(function(i) {
              let choice = {
                id            : $(this).find('input.choice-id').val().trim() != '' ? $(this).find('input.choice-id').val().trim() : null,
                side_a        : $(this).find('input.side-a').val(),
                side_b        : $(this).find('input.side-b').val()
              }
              if(choice.side_a != '' && choice.side_b != '')   task.data.push(choice)
            })
            break;
          case "code":
            //todo|debt - fix code type
            task.data.push({
              id          : element.find('.task-code input').val(),
              description : element.find('.task-code textarea').val()
            })
            break;
          default:
            //code block
        }
        return task
      }
    }

    $('.dropdown-custom .dropdown-menu li > a').click(function(e){
      e.preventDefault()
      const iconDOM = '<span class="caret"></span>'
      const value   = $(this).attr('data-dropdown-value')
      const valueUI = $(this).text()
      $(this).closest('li').addClass('preactive')
      let parent    = $(this).closest('.dropdown-custom')
      parent.find('.dropdown-menu li.active').removeClass('active')
      parent.find('.dropdown-menu li.preactive').removeClass('preactive').addClass('active')
      parent.find('.dropdown-toggle').html(valueUI+' '+iconDOM)
      parent.find('.dropdown-select').val(value)
    })
  </script>
@endsection
