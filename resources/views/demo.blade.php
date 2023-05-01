@extends('layouts.app')

@section('content')
<style>
    .trial-page-wrap{
        margin-top:30px;
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
    .demo-wrap .image-wrap{
        position: relative;
    }
    .trial-details-wrap .banner{
        font-size: 10px;
        font-weight: 700;
        margin: 0 0 20px;
        background-color: #ebe5ff;
        border: 1px solid #5d2cff;
        color: #5d2cff;
        padding: 5px 40px;
        width: calc(100% + 80px);
        position: relative;
        left:-55px;
        border-radius: 5px;
        border-bottom-left-radius: 0px;
    }
    .trial-details-wrap .banner::before{
        content: "";
        border: 5px solid;
        border-color: #5d2cff #5d2cff transparent transparent;
        position: absolute;
        left: -1px;
        bottom: -11px;
    }
    .trial-login-wrap .image-wrap img{
        position: absolute;
        right:60px;
        top:-60px;
        width: 30%;
    }
    .trial-login-wrap .image-wrap.reverse img{
        bottom:0px;
        top: auto;
    }
    .trial-login-wrap button{
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
    .trial-details-wrap{
        background-color: #fff;
        padding-top: 30px;
    }
    .trial-details-wrap .list-wrap img{
        width:20px;
        margin-right:10px;
        float:left;
    }
    .trial-details-wrap .list-item{
        color: #636B6F;
        font-size: 16px;
        width: 100%;
    }
    .trial-details-wrap .list-item div{
        float:left;
        width: calc(100% - 30px);
    }
    .trial-details-wrap .list-item.list-item-small{
        font-size: 14px;
    }

    .trial-details-wrap hr{
        float: left;
        width: 100%;
    }



    /*.padding-left-40{*/
    /*    padding-left: 40px;*/
    /*}*/
</style>

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
