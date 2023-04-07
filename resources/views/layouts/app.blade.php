<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=greek" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>

        /* todo|debt - move these to app.css */
        .banner{
            font-size: 10px;
            font-weight: 700;
            margin: 0 0 20px;
            padding: 5px 15px;
            position: relative;
            left:-15px;
            top:14px;
            border-radius: 5px;
        }
        .banner-yellow{
            background-color: #FFF2C7;
            border: 1px solid #F0B400;
            color: #F0B400;
        }
        .banner-blue{
            background-color: #b8cdfc;
            border: 1px solid #3765cd;
            color: #3765cd;
        }
    </style>
    @yield('styles')
</head>
<body>
<script type="text/javascript">
  window.CSRF = "{{csrf_token()}}";
  window.baseURL = "{{URL::to('/')}}";
  window.userData = null;
  @if(!Auth::guest())
    window.userData =  {!! json_encode(Auth::user()) !!};
    @endif
</script>
<div id="app">
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <a class="navbar-brand" href="{{ url('/') }}">
                    @include('includes.navbar_logo')
                </a>
                @if(\App\Util\Demo::shouldShowModeBanner(\App\Util\Demo::DEMO))
                    <span class="banner banner-blue">D E M O</span>
                @elseif(App\Util\Demo::shouldShowModeBanner(\App\Util\Demo::TRIAL))
                    <span class="banner banner-yellow">T R I A L</span>
                @endif
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    @php $user = Auth::user(); @endphp
                    @if ($user)
                        @if($user->can('accessUsers'))
                            <li class="{{ Request::is('users') || Request::is('users/*') ? 'active' : '' }}">
                                <a href="{{ url('/users') }}">Users</a>
                            </li>
                        @endif
                        @if($user->can('accessLessons'))
                            <li class="{{ Request::is('lessons') || Request::is('lessons/*') ? 'active' : '' }}">
                                <a href="{{ url('/lessons') }}">Courses</a>
                            </li>
                        @endif
                        @if($user->can('accessTests'))
                            <li class="{{ Request::is('tests') || Request::is('tests/*') ? 'active' : '' }}">
                                <a href="{{ url('/tests') }}">Tests</a>
                            </li>
                        @endif
                        @if($user->can('accessSegments'))
                            <li class="{{ Request::is('segments') || Request::is('segments/*') ? 'active' : '' }}">
                                <a href="{{ url('/segments') }}">Segments</a>
                            </li>
                        @endif
                    @endif
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        @if(!\Illuminate\Support\Facades\Request::is('public/*'))
                            {{session('demo_user')}}
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @endif
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                <span class="label label-{{Auth::user()->role}}">{{ucfirst(Auth::user()->role)}}</span> {{ Auth::user()->name }}
                                <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{url('/settings')}}"><i class="fa fa-cog"></i> Settings</a></li>

                                @if($user->can('viewStatistics'))
                                    <li><a href="{{url('/statistics')}}"><i class="fa fa-bar-chart"></i> Statistics</a></li>
                                @endif

                                @php
                                    $mode = \App\Util\Demo::getModeFromSessionIfAny();
                                @endphp
                                @if(!is_null($mode) && \App\Util\Demo::shouldBeAbleToSwitchRole())
                                    <li role="separator" class="divider"></li>
                                    <li class="dropdown-header">{{ucfirst($mode)}} Options</li>
                                    @foreach(\App\Enums\UserRole::values() as $role)
                                        @php $toggle = $role == Auth::user()->role ? 'on' : 'off'; @endphp
                                        <li>
                                            <a href="{{ url($mode.'/switch-role/'.$role) }}"
                                               onclick="event.preventDefault();
                                                         document.getElementById('switch-role-{{$role}}-form').submit();"><i class="fa fa-toggle-{{$toggle}}"></i> Switch to <span class="label label-{{$role}}">{{ucfirst($role)}}</span>
                                            </a>
                                            <form id="switch-role-{{$role}}-form" action="{{ url($mode.'/switch-role/'.$role) }}" method="POST"
                                                  style="display: none;">
                                                <input type="hidden" value="{{ csrf_token() }}" name="_token">
                                            </form>
                                        </li>
                                    @endforeach
                                    <li role="separator" class="divider"></li>
                                @endif
                                <li>
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        <i class="fa fa-sign-out"></i> Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    <div class="container wrap-for-banners">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                {{ session('error') }}
            </div>
        @elseif ($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <div class="content-wrap">
        @yield('content')
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirmLabel" id="confirm-modal">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="confirmLabel">Confirm</h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <input type="hidden" class="form-to-submit"/>
                    <button type="button" class="btn btn-danger yes-confirm" data-dismiss="modal">Yes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
@yield('scripts')

</body>
</html>
