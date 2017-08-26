@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Exams Portal</div>

                <div class="panel-body">
                    @if(Auth::check())
                        You are logged in!ssss
                    @else
                        You must login in order to do more things.
                    @endif
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Exams Portal</div>

                <div class="panel-body">
                    @if(Auth::check())
                        You are logged in!s
                    @else
                        You must login in order to do more things.
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
