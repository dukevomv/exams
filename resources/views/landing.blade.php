@extends('layouts.landing')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="margin-bottom-30">Run your class exams with automatic grading. <a href="{{URL::to('/login')}}" class="btn btn-lg btn-default pull-right">Login</a></h1>
                <p class="text-lg margin-bottom-30">This platform has started from a paper on e-assessment and polished to support actual examination from freelance professors with small classes <br>to big organisations with the platform set on-premises.</p>

                @include('includes.jumbotrons.intro')
                <div class="col-xs-12 no-padding margin-bottom-30 margin-top-30">
                    <div class="col-xs-12 col-md-6 no-padding">
                        <img src="{{URL::to('images/landing/question-types.png')}}" class="margin-bottom-15 img-thumbnail">
                        <p class="text-lg">Compose multipart segments with different Question types that all can be automatically graded based on the student's answers and the pre-provided correct answers.</p>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <img src="{{URL::to('images/landing/correspondence.png')}}" class="margin-bottom-15 img-thumbnail">
                    </div>
                </div>
                <div class="col-xs-12 no-padding margin-bottom-30 margin-top-30">
                    <div class="col-xs-12 col-md-8 no-padding">
                        <img src="{{URL::to('images/landing/student-invites.png')}}" class="img-thumbnail margin-bottom-30">
                    </div>
                    <div class="col-xs-12 col-md-4">
                        <p class="text-lg">Invite students to your exam by email, control who is participating directly on the examination.</p>
                    </div>
                </div>
                <div class="col-xs-12 no-padding margin-bottom-30 margin-top-30">
                    <div class="col-xs-12 col-md-9">
                        <p class="text-lg">Control the Exam by starting and stopping the timer for your students.</p>
                        <br><p class="text-lg">Always have visibility on the currently active student's status and remaining time.</p>
                        <br><p class="text-lg">Students can save their answers as draft without publishing them for grading in order to avoid technical issues to their pending submissions.</p>
                    </div>
                    <div class="col-xs-12 col-md-3 no-padding">
                        <img src="{{URL::to('images/landing/timer.png')}}" class="img-thumbnail">
                    </div>
                </div>
            </div>
        </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    This platform is for testing only, If you want to set it up on-premise feel free to contact me at <a href="mailto:dukevomv@gmail.com">dukevomv@gmail.com</a>.
                </div>
            </div>
    </div>
@endsection
