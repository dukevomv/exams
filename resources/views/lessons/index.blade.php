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
                <th>Subscribed</th>
              @else
                <th>Status</th>
              @endif
              <th>Actions</th>
            </tr>
            @foreach($lessons as $lesson)
              <tr>
                <td>{{$lesson->name}}</td>
                <td>{{$lesson->semester}}</td>
                <td>{{$lesson->gunet_code}}</td>
                <td>
                @if(Auth::user()->role == 'admin')
                  <span class="label label-professor">2</span>
                  <span class="label label-student">5</span>
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
                    <button class="btn btn-primary btn-xs">
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
                      <button type="button" class="btn btn-success btn-xs" disabled>Subscribe</button>
                    @elseif($lesson->status->approved == 0)
                      <button type="button" class="btn btn-danger btn-xs" disabled>Cancel</button>
                    @elseif($lesson->status->approved == 1)
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
  
@endsection


@section('scripts')
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>
  <script type="text/javascript">
    const basic_url = "{{url('lessons')}}";
    
    function InitUpdateLessonModal(id=null){
      var editableFields = ['id','name','gunet_code','semester'];
      var modal = $('.lesson-update-modal')
      if(id == null){
        modal.find('span.action-update').addClass('hidden')
        modal.find('span.action-create').removeClass('hidden')
        $.each( editableFields, function(key,field) {
          modal.find('input[name="'+field+'"]').val('')
        });
        modal.modal('show')
      } else {
        modal.find('span.action-create').addClass('hidden')
        modal.find('span.action-update').removeClass('hidden')
        $.ajax({
          type: "GET",
          url: basic_url+'/'+id,
          success: function(data){
            $.each( editableFields, function(key,field) {
              modal.find('input[name="'+field+'"]').val(data[field])
            });
            modal.modal('show')
          }
        })
      }
    }
  </script>
@endsection

