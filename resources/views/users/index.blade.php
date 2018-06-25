@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="row-margin-bottom col-xs-12">
        <div class="row">
          <div class="col-xs-9">
            <div class="btn-group pull-left">
              <button id="role" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ucfirst(Request::input('role','all'))}} <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li @if(Request::input('role','') == '')class="active"@endif><a href="{{route('users_index',Request::except('page'))}}">All</a></li>
                <li @if(Request::input('role','') == 'admin')class="active"@endif><a href="{{route('users_index',array_merge(Request::except('page'),['role'=>'admin']))}}">Admins</a></li>
                <li @if(Request::input('role','') == 'professor')class="active"@endif><a href="{{route('users_index',array_merge(Request::except('page'),['role'=>'professor']))}}">Professors</a></li>
                <li @if(Request::input('role','') == 'student')class="active"@endif><a href="{{route('users_index',array_merge(Request::except('page'),['role'=>'student']))}}">Students</a></li>
              </ul>
            </div>
            <div class="btn-group margin-left-15 pull-left">
              <button type="button" data-toggle="modal" data-target="#invite-user" class="btn btn-primary" >
                <i class="fa fa-plus"></i> Invite
              </button>
            </div>
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
              <th>Email</th>
              <th>Role</th>
              <th>Approved</th>
            </tr>
            @foreach($users as $user)
              <tr>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td><span class="label label-{{$user->role}}">{{ucfirst($user->role)}}</span></td>
                <td>
                  @include('includes.assets.toggle', [
                    'classes'=>['approved-toggle'],
                    'attributes'=>['data-user-id'=>$user->id],
                    'active' => $user->approved == 1
                  ])
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
          {{ $users->appends(Request::except('page'))->links() }}
        </nav>
      </div>
    </div>
  </div>

  <div class="modal fade" id="invite-user" tabindex="-1" role="dialog" aria-labelledby="invite_user">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="invite_user">Invite User</h4>
        </div>
        <div class="modal-body">
          <h4>Email</h4>
          <label>
            <input type="email" class="form-control" required>
          </label>
          <h4>Role</h4>
          <label>
            <input type="radio" name="invite-role" value="admin" @if(Request::input('role','') == 'admin') checked @endif> Admin
          </label><br>
          <label>
            <input type="radio" name="invite-role" value="professor" @if(Request::input('role','') == 'professor') checked @endif> Professor
          </label><br>
          <label>
            <input type="radio" name="invite-role" value="student" @if(Request::input('role','') == 'student') checked @endif> Student
          </label>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Invite</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>  
  $('.approved-toggle input').on('change',function(){
    let input = $(this)
    const value = input.prop('checked')
    let toggle = input.closest('.approved-toggle')
    $.post( 
      "{{url('users/invite')}}", 
      { 
        _token: '{{csrf_token()}}',
        user : toggle.attr('data-user-id'),
      }
    )
    .fail(function( data ) {
      setTimeout(function(){
        input.prop('checked',!value)
    },200)
    });
  })
</script>
@endsection