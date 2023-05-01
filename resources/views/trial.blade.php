@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row trial-page-wrap">
        <div class="col-xs-12 col-md-6">
            <div class="jumbotron trial-details-wrap">
                <div class="row">
                    <span class="banner">T R I A L</span>
                    <h4><br>Perform a live examination online.<br>Create a test for students, and get instant results!</h4>
                    <div class="text-lg list-wrap">
                        <br>
                        <div class="list-item"><img src="{{asset('images/check@2x.png')}}"> No Signup Required</div>
                        <div class="list-item"><img src="{{asset('images/check@2x.png')}}"> No credit card required</div>
                        <div class="list-item"><img src="{{asset('images/check@2x.png')}}"> Unlimited trial preparation period</div>
                        <div class="list-item"><img src="{{asset('images/check@2x.png')}}"> +30 days post examination data retention</div>
                        <hr>
                        <div class="list-item"><img src="{{asset('images/check@2x.png')}}"> Compose an Exam</div>
                        <div class="list-item"><img src="{{asset('images/check@2x.png')}}"> Invite students to participate</div>
                        <div class="list-item"><img src="{{asset('images/check@2x.png')}}"> Start Examination with all </div>
                        <div class="list-item"><img src="{{asset('images/check@2x.png')}}"> Preview Live all students with accepted invitation</div>
                        <div class="list-item"><img src="{{asset('images/check@2x.png')}}"> Start & Finish examination</div>
                        <div class="list-item"><img src="{{asset('images/check@2x.png')}}"> Evaluate results automatically</div>
                        <div class="list-item"><img src="{{asset('images/check@2x.png')}}"> Send each student's results to their email</div>
                    </div>
                    <div class="row">
                        <div class="row">
                            <div class="row">
                                <hr>
                            </div>
                        </div>
                    </div>
                    <p>
                    <h5>Fill the form below to prepare your Trial Exam</h5>
                    <form action="{{ url('/trial/generate') }}" method="POST">
                        <input type="hidden" value="{{ csrf_token() }}" name="_token">
                        <div class="row">
                            <div class="col-xs-12">
                                <input type="email" name="trial_email" class="form-control input-lg" placeholder="Your Email" value="{{ old('trial_email') }}" required>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-12">
                                <input type="text" name="course_name" class="form-control input-lg" placeholder="Course Name" value="{{ old('course_name') }}" required>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-6">
                                <input type="number" name="duration_in_minutes" class="form-control input-lg" placeholder="Duration (mins)"  value="{{ old('duration_in_minutes') }}" required>
                            </div>
                            <div class="col-xs-6">
                                <input type="datetime-local" name="scheduled_at" class="form-control input-lg" placeholder="Examination Date"  value="{{ old('scheduled_at') }}" required>
                            </div>
                        </div>
                        <br>
                        <input type="text" name="reason" class="form-control input-lg" placeholder="How did you find us?" value="{{ old('reason') }}" required>
                        <br>
                        <button type="submit" class="btn btn-primary btn-lg pull-right" role="button">Start Trial
                        </button>
                    </form>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="row">
                <div class="col-xs-12">
                    @include('includes.jumbotrons.trial_login')
                </div>
                <div class="col-xs-12">
                    @include('includes.jumbotrons.demo_intro')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
