@if(config('app.demo.enabled') && !Session::has('demo'))
    <div class="jumbotron hidden">
        <h1>Trial Mode</h1>
        <p>Perform a live examination online. <br>Create a test for your class to take, and get <b>instant results</b>.</p>
        <ul class="text-lg">
            <li><b>Skip Signup flow</b></li>
            <li><b>No payments required</b></li>
            <li>You will need to provide the date and time of your test, along with the duration (up to 90 minutes).</li>
            <li>A <b>User</b> will be created with role Professor which will have the ability to start and finish the test.</li>
            <li>You will be able to create Segments that will be used in your test containing all the questions of the test.</li>
            <li>Then edit the exising test, and attach the segments you've created earlier.</li>
            <li>Create an email list with your students so you can invite them.</li>
            <li>By saving the test as published, you will allow your invite list to subscribe to your test, and participate</li>
            <li>After the test has finished you will be able to grade the results and get a full report email.</li>
            <li>We will also email each student with their results.</li>
            <li>After 30 days, this account and it's data will be lost forever.</li>
        </ul>
        <hr>
        <p>
            <h4>Generate Trial for the first time</h4>
            <form action="{{ url('/trial/generate') }}" method="POST">
                <input type="hidden" value="{{ csrf_token() }}" name="_token">
                <div class="row">
                    <div class="col-xs-6">
                        <input type="email" name="trial_email" class="form-control input-lg" placeholder="Your Email" value="{{ old('trial_email') }}" required>
                    </div>
                    <div class="col-xs-6">
                        <input type="text" name="course_name" class="form-control input-lg" placeholder="Course" value="{{ old('course_name') }}" required>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-xs-6">
                        <input type="datetime-local" name="scheduled_at" class="form-control input-lg" placeholder="Examination Date"  value="{{ old('scheduled_at') }}" required>
                    </div>
                    <div class="col-xs-6">
                        <input type="number" name="duration_in_minutes" class="form-control input-lg" placeholder="Duration (mins)"  value="{{ old('duration_in_minutes') }}" required>
                    </div>
                </div>
                <br>
                <input type="text" name="reason" class="form-control input-lg" placeholder="Reason of test (school, trial, friend dropdown)" value="{{ old('reason') }}" required>
                <br>
                <button type="submit" class="btn btn-info btn-lg pull-right" role="button">Start Trial
                </button>
            </form>
                <br>
        <br>
        </p>
        <hr>
        <p>
        <h4>Get Access to active Trial</h4>
        <form action="{{ url('/trial/send-login-code') }}" method="POST">
            <input type="hidden" value="{{ csrf_token() }}" name="_token">
            <div class="row">
                <div class="col-xs-6">
                    <input type="email" name="trial_email" class="form-control input-lg" placeholder="Your Email" value="{{ old('trial_email') }}" required>
                </div>
                <div class="col-xs-6">
                    <button type="submit" class="btn btn-info btn-lg pull-right" role="button">Send Login code for Trial</button>
                </div>
            </div>
        </form>
        <br>
        </p>
    </div>

    <style>
        .trial-demo-wrap{
            margin-top: 30px;
        }
        .trial-details-wrap .list-wrap img{
            width:20px;
            margin-right:10px;
        }
        .trial-login-wrap{
            background-color: #FFF2C7;
            margin-top:50px;
            padding-top:30px;
            padding-bottom: 52px;
        }
        .demo-wrap{
            background-color: #EBE5FF;
            padding-top:30px;
            padding-bottom: 52px;
        }
        .demo-wrap a{
            color:#3B00FF;
        }
        .trial-login-wrap a{
            color:#3c4ee4;
        }
        .demo-wrap .image-wrap{
            position: relative;
        }
        .trial-login-wrap .image-wrap img{
            position: absolute;
            right:60px;
            top:-60px;
            width: 30%;
        }
        .demo-wrap .image-wrap img{
            position: absolute;
            right:0px;
            bottom:-80px;
            width: 25%;
        }
        .jumbotron p{
            font-size:16px;
        }
    </style>
    <div class="col-xs-12 trial-demo-wrap">
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="jumbotron demo-wrap">
                    <div class="row">
                        <div class="row">
                            <div class="col-xs-12">
                                <h3>See how it works</h3>
                                <p>Create auto-generated data and explore our features using different roles like <b>Admin</b>, <b>Professor</b> and <b>Student</b>. Manage Exams, Courses and Users in a sandbox environment to <b>understand the Exams Studio's capabilities</b>.</p>
                            </div>
                        </div>
                        <a href="https://demo.exams.studio">Explore Demo <i class="fa fa-arrow-right"></i></a>
                    </div>
                    <div class="image-wrap">
                        <img width="150px" src="{{asset('images/stars@2x.png')}}" alt="">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="jumbotron trial-login-wrap">
                    <div class="row">
                        <div class="row">
                            <div class="col-xs-8">
                                <h3>Generate Trial Exam</h3>
                                <p>Perform a real examination with student invites on a future date.<br>You will need to generate the Exam questions after setup.</p>
                                <a href="{{url('trial')}}">Setup Trial Exam <i class="fa fa-arrow-right"></i></a>
                            </div>
                        </div>
                        <div class="image-wrap">
                            <img width="100px" src="{{asset('images/professor.png')}}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif