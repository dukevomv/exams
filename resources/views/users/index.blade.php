@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="row-margin-bottom col-xs-12">
        <div class="row">
          <div class="col-xs-9">
            <div class="btn-group pull-left">
              <button id="role" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ucfirst(Request::input('role','role'))}} <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li @if(Request::input('role','') == '')class="active"@endif><a href="{{route('users_index',Request::except(['page','role']))}}">All</a></li>
                <li @if(Request::input('role','') == 'admin')class="active"@endif><a href="{{route('users_index',array_merge(Request::except('page'),['role'=>'admin']))}}">Admins</a></li>
                <li @if(Request::input('role','') == 'professor')class="active"@endif><a href="{{route('users_index',array_merge(Request::except('page'),['role'=>'professor']))}}">Professors</a></li>
                <li @if(Request::input('role','') == 'student')class="active"@endif><a href="{{route('users_index',array_merge(Request::except('page'),['role'=>'student']))}}">Students</a></li>
              </ul>
            </div>
            <div class="btn-group margin-left-15 pull-left">
              <button id="approved" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{Request::input('approved','') == '' ? 'Status' : (Request::input('approved','') == '1' ? 'Approved' : 'Not Approved')}} <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li @if(Request::input('approved','') == '')class="active"@endif><a href="{{route('users_index',Request::except(['page','approved']))}}">All</a></li>
                <li @if(Request::input('approved','') == '1')class="active"@endif><a href="{{route('users_index',array_merge(Request::except('page'),['approved'=>'1']))}}">Approved</a></li>
                <li @if(Request::input('approved','') == '0')class="active"@endif><a href="{{route('users_index',array_merge(Request::except('page'),['approved'=>'0']))}}">Not Approved</a></li>
              </ul>
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
                  @if($user->id != Auth::user()->id)
                    @include('includes.assets.toggle', [
                      'classes'=>['approved-toggle'],
                      'attributes'=>['data-user-id'=>$user->id],
                      'active' => $user->approved == 1
                    ])
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
          {{ $users->appends(Request::except('page'))->links() }}
        </nav>
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
      "{{url('users/toggle-approve')}}", 
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