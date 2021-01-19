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

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                    @if(config('app.demo.enabled') && Session::has(config('app.demo.session_field')))
                        <span class="label label-info">DEMO</span>
                    @endif
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    @php $user = Auth::user(); @endphp
                    @if ($user)
                        @if(\App\Util\UserIs::admin($user))
                            <li class="{{ Request::is('users') || Request::is('users/*') ? 'active' : '' }}">
                                <a href="{{ url('/users') }}">Users</a>
                            </li>
                        @endif
                            <li class="{{ Request::is('lessons') || Request::is('lessons/*') ? 'active' : '' }}">
                                <a href="{{ url('/lessons') }}">Lessons</a>
                            </li>
                        @if(\App\Util\UserIs::professorOrStudent($user))
                            <li class="{{ Request::is('tests') || Request::is('tests/*') ? 'active' : '' }}">
                                <a href="{{ url('/tests') }}">Tests</a>
                            </li>
                        @endif
                        @if(\App\Util\UserIs::professor($user))
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
                        {{session('demo_user')}}
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                <span class="label label-{{Auth::user()->role}}">{{ucfirst(Auth::user()->role)}}</span> {{ Auth::user()->name }}
                                <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{url('/settings')}}"><i class="fa fa-cog"></i> Settings</a></li>

                                @if(\App\Util\UserIs::adminOrProfessor($user))
                                    <li><a href="{{url('/statistics')}}"><i class="fa fa-bar-chart"></i> Statistics</a></li>
                                @endif

                                @if(config('app.demo.enabled'))
                                    <li role="separator" class="divider"></li>
                                    <li class="dropdown-header">DEMO Options</li>
                                    @foreach(\App\Enums\UserRole::values() as $role)
                                        @php $toggle = $role == Auth::user()->role ? 'on' : 'off'; @endphp
                                    <li>
                                        <a href="{{ url('demo/switch-role/'.$role) }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('switch-role-{{$role}}-form').submit();"><i class="fa fa-toggle-{{$toggle}}"></i> Switch to <span class="label label-{{$role}}">{{ucfirst($role)}}</span>
                                        </a>
                                        <form id="switch-role-{{$role}}-form" action="{{ url('demo/switch-role/'.$role) }}" method="POST"
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
