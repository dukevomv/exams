@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row trial-page-wrap">
        <div class="col-xs-12 col-md-6">
            <div class="jumbotron trial-details-wrap">
                <div class="row">
                    <span class="banner">D E M O</span>
                    <h4><br>The list below contains the entities that will be created for your Demo.</h4>
                    <div class="text-lg list-wrap">
                        <br>
                        <div class="list-item"><img src="{{asset('images/check@2x.png')}}"> <div>Users of all roles approved in the platform: Admin, Professor, Student</div></div>
                        <div class="list-item"><img src="{{asset('images/check@2x.png')}}"> <div>You will be able to switch user roles from the top right user dropdown</div></div>
                        <hr>
                        <div class="list-item list-item-small"><img src="{{asset('images/check@2x.png')}}"> <div>A Course with Student and Professor registered on it</div></div>
                        <div class="list-item list-item-small"><img src="{{asset('images/check@2x.png')}}"> <div>A Draft exam for Professor with no students attached on it</div></div>
                        <div class="list-item list-item-small"><img src="{{asset('images/check@2x.png')}}"> <div>A Scheduled exam for Professor with Student registered on it</div></div>
                        <div class="list-item list-item-small"><img src="{{asset('images/check@2x.png')}}"> <div>A Started exam for Professor with Student currently taking it</div></div>
                        <div class="list-item list-item-small"><img src="{{asset('images/check@2x.png')}}"> <div>A Started expired for by Professor with Student currently taking it with count-down set to 0</div></div>
                        <div class="list-item list-item-small"><img src="{{asset('images/check@2x.png')}}"> <div>A just finished exam for Professor with Student answers still available for saving</div></div>
                        <div class="list-item list-item-small"><img src="{{asset('images/check@2x.png')}}"> <div>A Finished exam for Professor with Student answers pending for Professor grading</div></div>
                        <div class="list-item list-item-small"><img src="{{asset('images/check@2x.png')}}"> <div>2 Graded exams for Professor with Student graded answers</div></div>
                        <div class="list-item list-item-small"><img src="{{asset('images/check@2x.png')}}"> <div>34 more students will be attached to the tests below with auto generated answers</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="row">
                <div class="col-xs-12">
                    @include('includes.jumbotrons.demo_generate',['position'=>'reverse'])
                </div>
                <div class="col-xs-12">
                    @include('includes.jumbotrons.trial_intro',['position'=>'reverse'])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
