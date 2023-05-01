@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            @include('includes/assets/errors-banner')
            @php
                $trial = \Illuminate\Support\Facades\Auth::user()->trials()->first();
                if(!is_null($trial)){
                    $trialTest = $trial->tests()->first();
                    $expDate = \Carbon\Carbon::parse($trialTest->scheduled_at)->addDays(30);
                    $expires = $expDate->isFuture() ? $expDate->diffInDays().' days' : 'Expired since '.$expDate->toDateString();
                }
            @endphp
            @if(!is_null($trial) && \App\Util\UserIs::professor(\Illuminate\Support\Facades\Auth::user()))
                <div class="panel panel-default">
                    <div class="panel-heading">Trial Details</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label>Unique Id</label>
                            <input type="text" class="form-control" value="{{$trial->uuid}}" disabled>
                        </div>
                        <div class="form-group">
                            <label>Trial Email</label>
                            <input type="text" class="form-control" value="{{$trial->email}}" disabled>
                        </div>
                        <div class="form-group">
                            <label>Trial Exam Name</label>
                            <input type="text" class="form-control" value="{{$trialTest->name}}" disabled>
                        </div>
                        <div class="form-group">
                            <label>Trial Exam Status</label>
                            <input type="text" class="form-control" value="{{$trialTest->status}}" disabled>
                        </div>
                        <div class="form-group">
                            <label>Examination Scheduled Date</label>
                            <input type="text" class="form-control" value="{{\Carbon\Carbon::parse($trialTest->scheduled_at)->toDateString()}}" disabled>
                        </div>
                        <div class="form-group">
                            <label>Expires in</label>
                            <input type="text" class="form-control" value="{{$expires}}" disabled>
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
                            <input type="text" name="name" class="form-control" id="inputName" placeholder="Name" value="{{Auth::user()->name}}" @if(!Auth::user()->can('updateProfileDetails')) disabled @endif>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail">Email address</label>
                            <input type="email" name="email" class="form-control" id="inputEmail" placeholder="Email" value="{{Auth::user()->email}}" @if(!Auth::user()->can('updateProfileDetails')) disabled @endif>
                        </div>
                        <button type="submit" class="btn btn-primary" @if(!Auth::user()->can('updateProfileDetails')) disabled @endif>Save Details</button>
                    </form>
                    <hr>
                    <form method="POST" action="{{url('settings/otp')}}">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <div class="form-group">
                            <label><input type="checkbox" name="otp_enabled"  @if(Auth::user()->otp_enabled) checked @endif @if(!Auth::user()->can('updateOTPSetting')) disabled @endif> Enable OTP</label>
                        </div>
                        <button type="submit" class="btn btn-primary" @if(!Auth::user()->can('updateOTPSetting')) disabled @endif>Confirm OTP Setting</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
