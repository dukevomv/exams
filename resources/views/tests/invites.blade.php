@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="row-margin-bottom col-xs-12">
        <div class="row">
          <div class="col-xs-9">
            <div class="btn-group pull-left margin-left-15">
              <button id="lesson" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php
                $selected_status = Request::input('status','All');
                foreach(\App\Enums\TestStatus::values() as $status){
                  if($selected_status == $status){
                    $selected_status = ucFirst($status);
                    break;
                  }
                }
                ?>
                Status: {{$selected_status}} <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li @if(Request::input('status','') == '')class="active"@endif><a href="{{route('tests_index',Request::except('page','status'))}}">All</a></li>
                <li role="separator" class="divider"></li>
                @foreach(\App\Enums\TestStatus::values() as $status)
                  <li @if(Request::input('status','') == $status)class="active"@endif>
                    <a href="{{route('tests_index',array_merge(Request::except('page'),['status'=>$status]))}}">{{ucfirst($status)}}</a>
                  </li>
                @endforeach
              </ul>
            </div>
            @if (Auth::user()->can('createTests'))
              <div class="btn-group margin-left-15 pull-left">
                <a href="{{url('tests/create')}}" type="button" class="btn btn-primary" >
                  <i class="fa fa-plus"></i> Create
                </a>
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
              <th>Student Name</th>
              <th>Student Email</th>
              <th>Invitation Status</th>
              <th>Created</th>
              <th>Last Updated</th>
              <th class="text-center">Actions</th>
            </tr>
            <form action="{{url('tests/'.$testId.'/invited-students')}}" method="POST">
              <tr>
                {{ csrf_field() }}
                <td><input name="student_name" type="text" class="form-control" placeholder="Name"  value="{{old('student_name')}}"></td>
                <td><input name="student_email" type="email" class="form-control" placeholder="Email" value="{{old('student_email')}}" required></td>
                <td><label for="send-invite-upon-invite"><input name="send_invite" id="send-invite-upon-invite" type="checkbox"> Send Invitation</label></td>
                <td></td>
                <td class="text-center">
                    <button class="btn btn-success " type="submit"><i class="fa fa-plus"></i></button>
                </td>
            </tr></form>
            @foreach($invites as $invite)
              <tr>
                <td>{{$invite->student_name}}</td>
                <td>{{$invite->student_email}}</td>
                <td>
                  @if($invite->invited_status)
                  {{ucfirst($invite->status)}}
                  @else
                    <a href="{{url('tests/'.$testId.'/invited-students/'.$invite->id.'/send-invite')}}" type="button" class="btn btn-info btn-sm">
                    <i class="fa fa-envelope"></i>
                  </a>
                  @endif
                </td>
                <td>{{\Carbon\Carbon::parse($invite->created_at)->diffForHumans()}}</td>
                <td>{{\Carbon\Carbon::parse($invite->updated_at)->diffForHumans()}}</td>
                <td class="text-center">
                  @if($invite->status !== \App\Models\TestInvite::ACCEPTED)
                    <a href="{{url('tests/'.$testId.'/invited-students/'.$invite->id.'/delete')}}" type="button" class="btn btn-danger btn-sm">
                      <i class="fa fa-trash"></i>
                    </a>
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
          {{ $invites->appends(Request::except('page'))->links() }}
        </nav>
      </div>
    </div>
  </div>
@endsection
