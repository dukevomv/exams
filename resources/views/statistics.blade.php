@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('includes/assets/errors-banner')
            <div class="col-md-4">
                <div class="jumbotron text-center">
                    <h1>{{$lessons}} </h1>
                    <h4>Total Courses</h4>
                    <h1><i class="fa fa-book" aria-hidden="true"></i></h1>
                </div>
            </div>
            <div class="col-md-4">
                <div class="jumbotron text-center">
                    <h1>{{$tests}}</h1>
                    <h4>Total Tests</h4>
                    <h1><i class="fa fa-file-text" aria-hidden="true"></i></h1>
                </div>
            </div>
            <div class="col-md-4">
                <div class="jumbotron text-center">
                    <h1>{{$segments}}</h1>
                    <h4>Total Test Segments</h4>
                    <h1><i class="fa fa-check-square" aria-hidden="true"></i></h1>
                </div>
            </div>
            <div class="col-md-12">
                <div class="jumbotron text-center col-md-12">
                    <div class="col-md-6">
                        <h1>{{$users['total']}}</h1>
                        <h4>Total Users</h4>
                        <h1><i class="fa fa-users" aria-hidden="true"></i></h1>
                    </div>
                    <div class="col-md-6">
                        <h3>{{$users['admin']}} <span class="label label-admin">Admin</span></h3>
                        <br>
                        <h3>{{$users['professor']}} <span class="label label-professor">Professor</span></h3>
                        <br>
                        <h3>{{$users['student']}} <span class="label label-student">Student</span></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <blockquote>
                    <p>This page is only available to Admins and Professors</p>
                </blockquote>
            </div>
        </div>
    </div>
@endsection
