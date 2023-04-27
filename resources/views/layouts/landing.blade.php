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
        body{
            background-color: #fff;
        }
        .logo-wrap{
            padding: 30px;
        }
        .logo-wrap img{
            padding: 30px 0;
        }
        p.text-lg{
font-size: x-large;
        }
    </style>
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
    <div class="logo-wrap col-xs-12">
        <a class="col-xs-12 text-center" href="{{ url('/') }}">
            <img width="300px" class="text-center" src="{{URL::to('images/exams_studio.svg')}}" alt="Logo">
        </a>
    </div>
    <div class="content-wrap">
        @yield('content')
    </div>
</div>
<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
@yield('scripts')

</body>
</html>
