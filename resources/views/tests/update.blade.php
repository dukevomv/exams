@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <h1>@if($test) Edit @else Create @endif Test</h1>
      </div>
      <div class="sidebar-toolbar col-xs-3">
        <h5>Actions</h5>
        <div class="btn-group margin-bottom-15">
          <a @if($test) href="{{url('tests/'.$test->id)}}" @else disabled @endif type="button" class="btn btn-default">
            <i class="fa fa-eye"></i> Preview
          </a>
        </div>
        <div class="btn-group margin-bottom-15">
          <button type="button" id="save-btn" class="btn btn-primary">
            <i class="fa fa-save"></i> Save
          </button>
        </div>
  
        <h5>Segments <span class="segment-amount">()</span> <div class="btn-group btn-group-sm margin-left-15" role="group"><button type="button" class="btn btn-default segment-filter-trigger" data-toggle="modal" data-target=".segment-filter-modal"><i class="fa fa-filter"></i> Filters</button></div></h5>
        <div class="segments-wrap"></div>
  
      </div>
      <div class="col-md-9" id="test-body">
        <div class="panel panel-default basics-wrap relative">
          <input type="hidden" id="test-id" @if($test) value="{{$test->id}}" @endif>
          
          <div class="panel-heading">Basic Information</div>
          <div class="panel-body">
            <div class="col-md-8 row-margin-bottom">
              <label>Name:</label>
              <input type="text" class="form-control" @if($test) value="{{$test->name}}" @endif id="test-name" placeholder="Test about HTML">
            </div>
            
            <div class="col-md-4 row-margin-bottom">
              <label>Course:</label>
              <div class="btn-group dropdown-custom col-xs-12 no-padding">
                <button type="button" class="btn btn-default dropdown-toggle btn-block btn-dropdown-overflow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span class="btn-label">@if($test) {{$lessons->where('id', $test->lesson_id)->first()->name}} @else Select Course @endif</span>
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <li class="@if(!$test) active @endif dropdown-value-default"><a href="#" data-dropdown-value="default">Select Course</a></li>
                  @foreach($lessons as $lesson)
                    <li @if($test && $test->lesson_id == $lesson->id) class="active" @endif><a href="#" data-dropdown-value="{{$lesson->id}}">{{$lesson->name}}</a></li>
                  @endforeach
                </ul>
                <select class="hidden dropdown-select" id="test-lesson">
                  <option value="default">Select Course</option>
                  @foreach($lessons as $lesson)
                    <option value="{{$lesson->id}}"  @if($test && $test->lesson_id == $lesson->id) selected @endif>{{$lesson->name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            
            <div class="col-md-4 row-margin-bottom">
              <label>Scheduled at:</label>
              <?php 
                $scheduled = null;
                if($test && !is_null($test->scheduled_at))
                  $scheduled = Carbon\Carbon::parse($test->scheduled_at)->format('Y-m-d\TH:i');
              ?>
              <input type='datetime-local' class="form-control" id="test-scheduled" @if($scheduled) value="{{$scheduled}}" @endif/>
            </div>
            
            <div class="col-md-4 row-margin-bottom">
              <label>Duration (mins):</label>
              <input type="number" class="form-control" id="test-duration" placeholder="60" @if($test) value="{{$test->duration}}" @endif/>
            </div>
            
            <div class="col-md-4 row-margin-bottom">
              <label>Status:</label>
              <div class="btn-group dropdown-custom col-xs-12 no-padding">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  @if($test) {{ucfirst($test->status)}} @else Select Status @endif <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <li class="@if(!$test) active @endif dropdown-value-default"><a href="#" data-dropdown-value="default">Select Status</a></li>
                  <li @if($test && $test->status == 'published') class="active" @endif><a href="#" data-dropdown-value="published">Published</a></li>
                  <li @if($test && $test->status == 'draft') class="active" @endif><a href="#" data-dropdown-value="draft">Draft</a></li>
                </ul>
                <select class="hidden dropdown-select" id="test-status">
                  <option value="default">Select Status</option>
                  <option value="published"  @if($test && $test->status == 'published') selected @endif>Draft</option>
                  <option value="draft"  @if($test && $test->status == 'draft') selected @endif>Published</option>
                </select>
              </div>
            </div>
            
            <div class="col-md-12">
              <label>Description:</label>
              <textarea type="text" class="form-control" id="test-description" placeholder="All you need to know about HTML...">@if($test){{$test->description}}@endif</textarea>
            </div>
          </div>
        </div>
        @if($test)
          @foreach($test->segments as $key=>$segment)
            <div class="panel panel-default segment-wrap segment-id-{{$segment->id}} relative" data-segment-id="{{$segment->id}}">
              <div class="order-wrap">
                <div class="order-trigger cursor-pointer" data-order-direction="up"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
                <div class="order-value">{{$key+1}}</div>
                <div class="order-trigger cursor-pointer" data-order-direction="down"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
              </div>
              <div class="panel-heading">{{$segment->title}} ({{$segment->tasks_count}} tasks) 
                <span class="trash-btn pull-right margin-left-15" onclick="ChangeTestSegments('remove',{{$segment->id}})"><i class="fa fa-trash"></i></span>
                <?php $custom_title = str_replace('"','`',str_replace("'","`",$segment->title));?>
                <button class="btn btn-default btn-xs pull-right" onclick="PreviewSegmentInModal({{$segment->id}},'{{$custom_title}}',{{$segment->tasks_count}})"><i class="fa fa-eye"></i></button>
              </div>
            </div>
          @endforeach
        @endif
      </div>
    </div>
  </div>
  <div class="modal fade segment-filter-modal" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel"><i class="fa fa-filter"></i> Segment Filters</h4>
        </div>
        <div class="modal-body">
          <input type="text" class="margin-bottom-15 form-control search-field" placeholder="Search">
          <div class="btn-group dropdown-custom">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              @if($test) {{$lessons->where('id', $test->lesson_id)->first()->name}} @else Course @endif <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              <li class="@if(!$test) active @endif dropdown-value-default"><a href="#" data-dropdown-value="default">Lesson</a></li>
              @foreach($lessons as $lesson)
                <li @if($test && $test->lesson_id == $lesson->id) class="active" @endif><a href="#" data-dropdown-value="{{$lesson->id}}">{{$lesson->name}}</a></li>
              @endforeach
            </ul>
            <select class="hidden dropdown-select lesson-field">
              <option value="default">Lesson</option>
              @foreach($lessons as $lesson)
                <option value="{{$lesson->id}}"  @if($test && $test->lesson_id == $lesson->id) selected @endif>{{$lesson->name}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" onclick="RefreshSidebarSegments(true)">Clear filters</button>
          <button type="button" class="btn btn-primary" onclick="RefreshSidebarSegments()">Update filters</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade segment-preview-modal" role="dialog">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content col-xs-12 no-padding">
        <div class="modal-header col-xs-12">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Segment Preview <button type="button" class="btn btn-sm btn-primary add-to-test" onclick="AddFromModalToTest()"><i class="fa fa-plus"></i> Add</button></h4>
        </div>
        <div class="modal-body col-xs-12" data-segment-id="" data-segment-title="" data-segment-task-count="">
        </div>
        <div class="modal-footer col-xs-12">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal" aria-label="Close">Close</button>
          <button type="button" class="btn btn-primary add-to-test" onclick="AddFromModalToTest()"><i class="fa fa-plus"></i> Add</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script type="text/javascript">
    const basic_url = "{{url('/')}}"
    let test_segment_ids = @if($test) {!! json_encode($test->segments->pluck('id')->all()) !!}; @else []; @endif

    function RefreshSidebarSegments(defaultFilters = false){
      const defaultDataFilters = {
        lesson_id: null,
        search: ''
      }
      let data4filter = Object.assign({}, defaultDataFilters);
      let filter_modal = $('.segment-filter-modal')
      if(defaultFilters){
        filter_modal.find('.search-field').val('')
        filter_modal.find('.lesson-field').val('')
      } else {
        data4filter.lesson_id = filter_modal.find('.lesson-field').val() != 'default' ? filter_modal.find('.lesson-field').val() : data4filter.lesson_id
        data4filter.search    = filter_modal.find('.search-field').val() != ''        ? filter_modal.find('.search-field').val() : data4filter.search
      }
      if(JSON.stringify(data4filter) === JSON.stringify(defaultDataFilters))
        $('.segment-filter-trigger').removeClass('changed-button')
      else
        $('.segment-filter-trigger').addClass('changed-button')
      $.ajax({
        type: "GET",
        url: "{{url('segments/sidebar')}}",
        dataType: "json",
        data: data4filter,
        success: function(data){
          $('.sidebar-toolbar .segment-amount').text('('+data.length+')')
          let segmentsDOM = ''
          data.forEach(function(segment) {
            const btn_type = test_segment_ids.includes(segment.id) ? 'success' : 'default'
            let segment_title = segment.title.replace(/(['"])/g,'`')
            segmentsDOM += '<div class="btn-group margin-bottom-15 margin-right-15">'
            segmentsDOM += '  <button type="button" class="preview-segment segment-id-'+segment.id+' btn btn-'+btn_type+'" onclick="PreviewSegmentInModal('+segment.id+',\''+segment_title+'\','+segment.tasks_count+')">'
            segmentsDOM += '    <span>'+segment.title+'</span>'
            segmentsDOM += '  </button>'
            segmentsDOM += '</div>'
          })
          $('.sidebar-toolbar .segments-wrap').html(segmentsDOM)
          filter_modal.modal('hide');
        },
        error: function(err){
          console.log(err)
        }
      })
    }
    RefreshSidebarSegments()

    function PreviewSegmentInModal(segment_id,segment_title,segment_task_count){
      let modal = $('.segment-preview-modal')
      $.ajax({
        type: "GET",
        url: basic_url+'/segments/'+segment_id+'/preview',
        data: {modal:1},
        success: function(data){
          modal.find('.modal-body')
          .attr('data-segment-id',segment_id)
          .attr('data-segment-title',segment_title)
          .attr('data-segment-task-count',segment_task_count)
          .html(data)
          if(test_segment_ids.includes(segment_id))
            modal.find('button.add-to-test').prop('disabled',true)
          else
            modal.find('button.add-to-test').prop('disabled',false)
          modal.modal('show')
        },
        error: function(err){
          console.log(err)
        }
      })
    }

    function AddFromModalToTest(){
      let modal = $('.segment-preview-modal')
      let modal_body = modal.find('.modal-body')
      let segment_data = {
        id: modal_body.attr('data-segment-id'),
        title: modal_body.attr('data-segment-title'),
        task_count: modal_body.attr('data-segment-task-count')
      }
      let segmentDOM = '<div class="panel panel-default segment-wrap segment-id-'+segment_data.id+' relative" data-segment-id="'+segment_data.id+'">'
      segmentDOM += '    <div class="order-wrap">'
      segmentDOM += '      <div class="order-trigger cursor-pointer" data-order-direction="up"><i class="fa fa-angle-up" aria-hidden="true"></i></div>'
      segmentDOM += '      <div class="order-value"></div>'
      segmentDOM += '      <div class="order-trigger cursor-pointer" data-order-direction="down"><i class="fa fa-angle-down" aria-hidden="true"></i></div>'
      segmentDOM += '    </div>'
      segmentDOM += '    <div class="panel-heading">'+segment_data.title+' ('+segment_data.task_count+' tasks) '
      segmentDOM += '      <span class="trash-btn pull-right margin-left-15" onclick="ChangeTestSegments('+"'remove'"+','+segment_data.id+')"><i class="fa fa-trash"></i></span>'
      segmentDOM += '      <button class="btn btn-default btn-xs pull-right" onclick="PreviewSegmentInModal('+segment_data.id+',\''+segment_data.title+'\','+segment_data.task_count+')"><i class="fa fa-eye"></i></button>'
      segmentDOM += '    </div>'
      segmentDOM += '  </div>'
      ChangeTestSegments('add',segment_data.id)
      $('#test-body').append(segmentDOM)
      modal.modal('hide')
      ReorderTestTasks()
    }

    function ChangeTestSegments(action,id){
      if(action == 'add'){
        test_segment_ids.push(parseInt(id))
        $('.sidebar-toolbar .segments-wrap .segment-id-'+id).removeClass('btn-default').addClass('btn-success')
      } else if(action == 'remove'){
        test_segment_ids = test_segment_ids.filter(e => e !== id)
        $('#test-body .segment-wrap.segment-id-'+id).remove()
        $('.sidebar-toolbar .segments-wrap .segment-id-'+id).removeClass('btn-success').addClass('btn-default')
        ReorderTestTasks()
      }
    }

    $(document).on('click','#test-body .segment-wrap .order-wrap .order-trigger',function(e){
      let direction = $(this).attr('data-order-direction')
      let current_task = $(this).closest('.segment-wrap')
      let target_task = null
      if(direction == 'up')
        target_task = current_task.prev('.segment-wrap')
      else if(direction == 'down')
        target_task = current_task.next('.segment-wrap')

      if(target_task.length > 0){
        current_task.detach()
        if(direction == 'up')
          current_task.insertBefore(target_task)
        else if(direction == 'down')
          current_task.insertAfter(target_task)
        ReorderTestTasks()
      }
    })

    function ReorderTestTasks(){
      $("#test-body .segment-wrap").each(function(index) {
        $(this).find('.order-wrap .order-value').text(index+1)
      })
      $(document).find(".segment-wrap .panel-body .task-list").sortable({
        appendTo: document.body,
        cursor: "move",
        items: "> .task-choice",
        placeholder: "choice-placeholder",
        opacity: 0.5,
        handle: '.choice-handle',
        update: function( event, ui ) {}
      });
    }

    $("#save-btn").on('click',function(e){
      let thisBtn = $(this)
      thisBtn.addClass('disabled')
      let test = new Test()
      test.UpdateSegments()
      console.log(test)
      if(true){
        console.log('sendin')
        $.ajax({
          type: "POST",
          url: "{{url('tests/update')}}",
          data: {_token:"{{csrf_token()}}",...test},
          error: function(data){
            showValidatorErrors(data)
            thisBtn.removeClass('disabled')
            thisBtn.prop('disabled',false)
          },
          success: function(data){
            thisBtn.removeClass('disabled')
            document.location = '/tests/'+data.id+'/edit';
          }
        })
      } else {
        $(this).removeClass('disabled')
      }
    })

    var Test = function test(){
      this.constructor
      this.id           = $('.basics-wrap #test-id').val().trim() != '' ? $('.basics-wrap #test-id').val().trim() : null
      this.lesson_id    = $('.basics-wrap #test-lesson').val() != 'default' ? parseInt($('.basics-wrap #test-lesson').val()) : null
      this.name         = $('.basics-wrap #test-name').val().trim()
      this.scheduled_at = $('.basics-wrap #test-scheduled').val()
      this.duration     = $('.basics-wrap #test-duration').val()
      this.description  = $('.basics-wrap #test-description').val().trim()
      this.status       = $('.basics-wrap #test-status').val()
      this.segments     = []
    }

    Test.prototype.Validate = function(){
      
      return true
    }
    Test.prototype.UpdateBasics = function(){
      return false
    }
    Test.prototype.UpdateSegments = function(){
      let segments = []
      $("#test-body .segment-wrap").each(function(index) {
        let segment_id = $(this).attr('data-segment-id')
        segments.push(segment_id)
      })
      this.segments = segments
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
      parent.find('.dropdown-toggle').html('<span class="btn-label">'+valueUI+'</span> '+iconDOM)
      parent.find('.dropdown-select').val(value)
    })
  </script>
@endsection
