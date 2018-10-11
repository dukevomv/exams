@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="row-margin-bottom col-xs-12">
        <div class="row">
          <div class="col-xs-9">
            <div class="btn-group pull-left">
              <button id="status" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ucfirst(Request::input('status','all'))}} <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li @if(Request::input('status','') == '')class="active"@endif><a href="{{route('lessons_index',Request::except('page'))}}">All</a></li>
                <li @if(Request::input('status','') == 'approved')class="active"@endif><a href="{{route('lessons_index',array_merge(Request::except('page'),['status'=>'approved']))}}">Approved</a></li>
                <li @if(Request::input('status','') == 'pending')class="active"@endif><a href="{{route('lessons_index',array_merge(Request::except('page'),['status'=>'pending']))}}">Pending</a></li>
                <li @if(Request::input('status','') == 'unsubscribed')class="active"@endif><a href="{{route('lessons_index',array_merge(Request::except('page'),['status'=>'unsubscribed']))}}">Unsubscribed</a></li>
              </ul>
            </div>
            @if(Auth::user()->role == 'admin')
              <div class="btn-group margin-left-15 pull-left">
                <button class="btn btn-primary" onClick="InitUpdateLessonModal()">
                  <i class="fa fa-plus"></i> Create
                </button>
              </div>
            @endif
          </div>
          <div class="col-xs-3">
            @include('includes.assets.search-wrap', ['value'=>Request::input('search','')])
          </div>
        </div>  
      </div>  
    </div>  
    <div class="row">
      <div class="col-xs-12">
        <div class="panel panel-default">
          <table class="table">
            <tr>
              <th>Name</th>
              <th>Semester</th>
              <th>Code</th>
              @if(Auth::user()->role == 'admin')
                <th>Approved</th>
                <th>Actions</th>
              @else
                <th>Status</th>
                <th>Approval</th>
              @endif
            </tr>
            @foreach($lessons as $lesson)
              <tr>
                <td>{{$lesson->name}}</td>
                <td>{{$lesson->semester}}</td>
                <td>{{$lesson->gunet_code}}</td>
                <td>
                @if(Auth::user()->role == 'admin')
                  @if($lesson->approved_professors_count > 0)
                    <span class="label label-professor">{{$lesson->approved_professors_count}}</span>
                  @endif
                  @if($lesson->approved_students_count > 0)
                    <span class="label label-student">{{$lesson->approved_students_count}}</span>
                  @endif
                @else
                  @if(is_null($lesson->status))
                    Unsubscribed
                  @elseif($lesson->status->approved == 0)
                    Pending
                  @elseif($lesson->status->approved == 1)
                    Approved
                  @endif
                @endif
                </td>
                <td>
                  @if(Auth::user()->role == 'admin')
                    <button class="btn btn-{{$lesson->pending_users_count == 0 ? 'default': 'primary'}} btn-xs" onClick="InitLessonUserApprovalModal({{$lesson->id}})">
                      <i class="fa fa-users"></i>
                    </button>
                    <button class="btn btn-default btn-xs" onClick="InitUpdateLessonModal({{$lesson->id}})">
                      <i class="fa fa-pencil"></i>
                    </button>
                    <a href="{{url('lessons/'.$lesson->id.'/delete')}}" type="button" class="btn btn-danger btn-xs">
                      <i class="fa fa-trash"></i>
                    </a>
                  @else
                    @if(is_null($lesson->status))
                      <a href="{{url('lessons/'.$lesson->id.'/approval/request')}}" type="button" class="btn btn-success btn-xs">
                        Request
                      </a>
                    @else
                      <a href="{{url('lessons/'.$lesson->id.'/approval/cancel')}}" type="button" class="btn btn-danger btn-xs @if($lesson->status->approved == 1) disabled @endif" @if($lesson->status->approved == 1) disabled @endif>
                        Revoke
                      </a>
                    @endif
                  @endif
                </td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <nav class="pull-right" aria-label="Page navigation">
          {{ $lessons->appends(Request::except('page'))->links() }}
        </nav>
      </div>
    </div>
  </div>
  
  
  <div class="modal fade lesson-update-modal" role="dialog">
    <div class="modal-dialog" role="document">
      <form method="POST" action="{{URL::to('lessons')}}">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><span class="action-create">Create</span><span class="action-update hidden">Update</span> Lesson</h4>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <div class="row-margin-bottom">
              <label>Name:</label>
              <input type="text" class="form-control" name="name" placeholder="Lesson Name" required>
            </div>
            <div class="row-margin-bottom">
              <label>Code:</label>
              <input type="text" class="form-control" name="gunet_code" placeholder="Code" required>
            </div>
            <div class="row-margin-bottom">
              <label>Semester:</label>
              <input type="number" class="form-control" name="semester" placeholder="Semester">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal" aria-label="Close">Cancel</button>
            <button type="submit" class="btn btn-primary"><span class="action-create">Create</span><span class="action-update hidden">Update</span></button>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <div class="modal fade lesson-users-modal" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Lesson Approval Requests</h4>
        </div>
        <div class="modal-body">
          <table class="table table-bordered">
            <tr><th>Name</th><th>Email</th><th>Role</th><th>Approved</th></tr>
          </table>
        </div>
      </div>
    </div>
  </div>
  
@endsection

@section('scripts')
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>
  <script type="text/javascript">
    var basic_url = "{{url('lessons')}}";
    var approvalsChanged = false
    
    function InitUpdateLessonModal(id=null){
      var editableFields = ['id','name','gunet_code','semester'];
      var modal = $('.lesson-update-modal')
      if(id == null){
        modal.find('span.action-update').addClass('hidden')
        modal.find('span.action-create').removeClass('hidden')
        $.each( editableFields, function(key,val) {
          modal.find('input[name="'+val+'"]').val('')
        });
        modal.modal('show')
      } else {
        modal.find('span.action-create').addClass('hidden')
        modal.find('span.action-update').removeClass('hidden')
        $.ajax({
          type: "GET",
          url: basic_url+'/'+id,
          success: function(data){
            $.each( editableFields, function(key,val) {
              modal.find('input[name="'+val+'"]').val(data[val])
            });
            modal.modal('show')
          }
        })
      }
    }
    
    function InitLessonUserApprovalModal(id){
      var modal = $('.lesson-users-modal')
      $.ajax({
        type: "GET",
        url: basic_url+'/'+id+'/users',
        success: function(data){
          var table = modal.find('.modal-body table');
          table.find('.custom-row').remove();
          $.each( data.users, function(key,value) {
            var row = '<tr class="custom-row">'
             row +=   '<td>'+value.name+'</td>'
             row +=   '<td>'+value.email+'</td>'
             row +=   '<td><span class="label label-'+value.role+'">'+value.role+'</span></td>'
             row +=   '<td>'
             row +=     '<label class="toggle" data-user-id="'+value.id+'" data-lesson-id="'+id+'">'
             row +=       '<input type="checkbox" '+ (value.pivot.approved == 1 ? 'checked' : '')+'>'
             row +=       '<span class="slider"></span></label></td>'
             row += '</tr>'
            table.append(row)
          });
          modal.modal('show')
        }
      })
    }
      
    $(document).on('change','.lesson-users-modal .custom-row .toggle input',function(){
      let input = $(this)
      const value = input.prop('checked')
      let toggle = input.closest('.toggle')
      $.post( 
        "{{url('lessons/users/toggle-approve')}}", 
        { 
          _token: '{{csrf_token()}}',
          lesson_id : toggle.attr('data-lesson-id'),
          user_id : toggle.attr('data-user-id'),
        }
      )
      .fail(function( data ) {
        setTimeout(function(){
          input.prop('checked',!value)
        },200)
      })
      .done(function(data){
        approvalsChanged = true;
      });
    })
    
    $('.lesson-users-modal').on('hidden.bs.modal', function (e) {
      if(approvalsChanged)
        location.reload()
    })
  </script>
@endsection

