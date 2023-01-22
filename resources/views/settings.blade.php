@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            @include('includes/assets/errors-banner')
            @php
                $trial = \Illuminate\Support\Facades\Auth::user()->trials()->first()
            @endphp
            @if(!is_null($trial))
                <div class="panel panel-default">
                    <div class="panel-heading">Trial Details</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label>Unique Id</label>
                            <input type="text" class="form-control" value="{{$trial->uuid}}" disabled>
                        </div>
                        <div class="form-group">
                            <label>Test Status</label>
                            <input type="text" class="form-control" value="{{'draft'}}" disabled>
                        </div>
                        <div class="form-group">
                            <label>Expires in</label>
                            <input type="text" class="form-control" value="{{'28 days'}}" disabled>
                        </div>
                    </div>
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">Basic Details</div>
                <div class="panel-body">
                    <form method="POST" action="{{url('settings')}}">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <div class="form-group">
                            <label for="inputName">Name</label>
                            <input type="text" name="name" class="form-control" id="inputName" placeholder="Name" value="{{Auth::user()->name}}">
                        </div>
                        <div class="form-group">
                            <label for="inputEmail">Email address</label>
                            <input type="email" name="email" class="form-control" id="inputEmail" placeholder="Email" value="{{Auth::user()->email}}">
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="otp_enabled"  @if(Auth::user()->otp_enabled) checked @endif> Enable OTP</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Details</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
