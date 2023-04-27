@if(config('app.demo.enabled'))
    {{--    todo|remove this when app.css is built --}}
    <style>
    .trial-page-wrap{
            margin-top:30px;
        }
        .trial-details-wrap{
            background-color: #fff;
            padding-top: 30px;
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
        .demo-wrap .image-wrap{
            position: relative;
        }
        .trial-details-wrap .banner{
            font-size: 10px;
            font-weight: 700;
            margin: 0 0 20px;
            background-color: #FFF2C7;
            border: 1px solid #F0B400;
            color: #F0B400;
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
            border-color: #C99617 #C99617 transparent transparent;
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
        .trial-details-wrap .list-item{
            color: #636B6F;
            font-size: 16px;
        }
    </style>
    <div class="col-xs-12 trial-demo-wrap">
        <div class="row">
            <div class="col-xs-12 col-md-6">
                @include('includes.jumbotrons.demo_intro')
            </div>
            <div class="col-xs-12 col-md-6">
                @include('includes.jumbotrons.trial_intro')
            </div>
        </div>
    </div>
@endif