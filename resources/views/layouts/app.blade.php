<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  @yield('styles')
</head>
<body>
  <div id="app">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    @if (Auth::guest())
                        <li>
                            <a href="{{ url('/home') }}">Home</a>
                        </li>
                    @elseif(Auth::user()->role == 'admin')
                        <li class="{{ Request::is('users') || Request::is('users/*') ? 'active' : '' }}">
                            <a href="{{ url('/users') }}">Users</a>
                        </li>
                        <li class="{{ Request::is('lessons') || Request::is('lessons/*') ? 'active' : '' }}">
                            <a href="{{ url('/lessons') }}">Lessons</a>
                        </li>
                    @elseif(Auth::user()->role == 'professor')
                        <li class="{{ Request::is('lessons') || Request::is('lessons/*') ? 'active' : '' }}">
                            <a href="{{ url('/lessons') }}">Lessons</a>
                        </li>
                        <li class="{{ Request::is('tests') || Request::is('tests/*') ? 'active' : '' }}">
                            <a href="{{ url('/tests') }}">Tests</a>
                        </li>
                        <li class="{{ Request::is('segments') || Request::is('segments/*') ? 'active' : '' }}">
                            <a href="{{ url('/segments') }}">Segments</a>
                        </li>
                    @elseif(Auth::user()->role == 'student')
                        <li class="{{ Request::is('lessons') || Request::is('lessons/*') ? 'active' : '' }}">
                            <a href="{{ url('/lessons') }}">Lessons</a>
                        </li>
                        <li class="{{ Request::is('tests') || Request::is('tests/*') ? 'active' : '' }}">
                            <a href="{{ url('/tests') }}">Tests</a>
                        </li>
                    @endif
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                               <span class="label label-{{Auth::user()->role}}">{{ucfirst(Auth::user()->role)}}</span> {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{url('/settings')}}"><i class="fa fa-cog"></i> Settings</a></li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        <i class="fa fa-sign-out"></i> Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
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
    <div class="container">
      @if (session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @elseif (session('error'))
        <div class="alert alert-danger">
          {{ session('error') }}
        </div>
      @endif
    </div>
    @yield('content')
  </div>

  <!-- Scripts -->
  <script src="{{ asset('js/app.js') }}"></script>

  @yield('scripts')
        
</body>
</html>
