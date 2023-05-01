@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="test-preview">
            <div class="row">
                <div class="main col-xs-12 main-panel">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4>{{$test->name}}</h4>
                            <p>{{$test->description}}</p>
                        </div>
                        <div class="panel-body">
                            <p><strong>Scheduled at: </strong>{{$test->scheduled_at}} ( {{$test->scheduled_at->diffForHumans()}} )</p>
                            <p><strong>Total duration: </strong>{{$test->duration}}</p>
                        </div>
                    </div>
                </div>
                <div class="main col-xs-12 main-panel">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h4><i class="fa fa-envelope"></i> Invitation</h4>
                        </div>
                        <div class="panel-body">
                            <p><strong>Status: </strong>{{$invite->status}}</p>

                            @if($invite->status === \App\Models\TestInvite::INVITED)
                                <form action="{{route('test.invitation.accept',['testId'=>$test->id,'inviteUuid'=>$invite->uuid])}}" method="POST">
                                    {{csrf_field()}}
                                    <div class="input-group">
                                        <input type="email" class="form-control" name="email" placeholder="Email" value="{{old('email')}}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-success" type="submit">Accept Invitation</button>
                                        </span>
                                    </div>
                                </form>
                            @elseif($invite->status === \App\Models\TestInvite::ACCEPTED)
                                <form action="{{route('test.invitation.login-code',['testId'=>$test->id,'inviteUuid'=>$invite->uuid])}}" method="POST">
                                    {{csrf_field()}}
                                    <div class="input-group">
                                        <input type="email" class="form-control" name="email" placeholder="Email" value="{{old('email')}}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" type="submit">Send Login Code</button>
                                        </span>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection