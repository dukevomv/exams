@if(config('app.demo.enabled'))
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
                        <a href="{{URL::to('/demo')}}">Explore Demo <i class="fa fa-arrow-right"></i></a>
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