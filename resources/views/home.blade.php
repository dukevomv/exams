@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                @if(Auth::guest())
                    <div class="jumbotron">
                        <h1>Hello Guest!</h1>
                        <p><b>Login</b> or <b>Register</b> in order to use this platform or to keep up with the
                            development News.</p>
                        <br>
                        <p>
                            <a class="btn btn-primary btn-lg" href="{{url('/login')}}" role="button">Login now</a>
                            <a class="btn btn-default btn-lg margin-left-15" href="{{url('/register')}}" role="button">Register</a>
                        </p>
                    </div>
                    @include('includes.demo-jumbotron')
                @else
                    <div class="jumbotron">
                        <h1>Hi, {{Auth::user()->name}}</h1>
                        <p><span class="label label-danger">NEW</span><br>The Current feature is added in the latest
                            group of updates.</p>
                        <p><span class="label label-primary">FRONTEND</span><br>Most likely to contain User Interface
                            changes that will provide new flows and functionalities to the user.</p>
                        <p><span class="label label-success">BACKEND</span><br>Contains implementation in the background
                            that fixes the infrastructure and might not be visible to the user.</p>
                    </div>
                <!--

                         <span class="label label-warning">new2</span>
                         <span class="label label-primary">FRONTEND</span>
                         <span class="label label-success">BACKEND</span>
                         <span class="label label-default">IN PROGRESS</span>
                         <span class="label label-danger">NEW</span>

                     -->


                    <div class="panel panel-default">
                        <div class="panel-heading">
                            User Administration &middot; <small class="text-warning">28 July 2018</small>
                            <div class="pull-right">
                                <span class="label label-success">BACKEND</span>
                                <span class="label label-primary">FRONTEND</span>
                                <span class="label label-danger">NEW</span>
                            </div>
                        </div>
                        <div class="panel-body">
                            <p>
                                <b>Admins</b> can now manage <a href="{{ url('/users') }}" target="_blank">Users</a> by
                                approving or disapproving current users.
                            </p>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            URL filters on Lists and Search &middot; <small class="text-warning">11 May 2018</small>
                            <div class="pull-right">
                                <span class="label label-primary">FRONTEND</span>
                                <span class="label label-danger">NEW</span>
                            </div>
                        </div>
                        <div class="panel-body">
                            <p>
                                <b>Filters</b> are available to all lists <b>Users</b>, <b>Lessons</b> and <b>Tests</b>
                                inside url for permanent and sharable filtering.
                            </p>
                            <p>
                                <b>Search</b> is also added to the lists and provides the ability to look into custom
                                fields based on the list.
                            </p>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Authorization &middot; <small class="text-warning">18 March 2018</small>
                            <div class="pull-right">
                                <span class="label label-success">BACKEND</span>
                                <span class="label label-danger">NEW</span>
                            </div>
                        </div>
                        <div class="panel-body">
                            <p>
                                Authorization for specific role is filtered dynamically and shows error page based on
                                the action.
                            </p>
                            <p>
                                Links from other roles that dont show in Navigation Bar are also filtered.
                            </p>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Routing based on role &middot; <small class="text-warning">22 February 2018</small>
                            <div class="pull-right">
                                <span class="label label-primary">FRONTEND</span>
                                <span class="label label-danger">NEW</span>
                            </div>
                        </div>
                        <div class="panel-body">
                            <p>
                                Tabs in the Navigation bar are different per <b>Role</b> and provide a Role hierarchy
                            </p>
                            <p>
                                Admins <b>></b> Professors <b>></b> Students
                            </p>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            User registration based on type &middot; <small class="text-warning">04 February
                                2018</small>
                            <div class="pull-right">
                                <span class="label label-success">BACKEND</span>
                                <span class="label label-primary">FRONTEND</span>
                                <span class="label label-danger">NEW</span>
                            </div>
                        </div>
                        <div class="panel-body">
                            <p>
                                The <a href="{{ url('/register') }}" target="_blank">Register</a> page, now has
                                <b>Role</b> option for Admin, Professors or Students.
                            </p>
                            <p>
                                All Users that register are saved in a <b>pending</b> state and must be approved by an
                                existing approved user in the platform.
                            </p>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Tests Logic &middot; <small class="text-warning">12 November 2017</small>
                            <div class="pull-right">
                                <span class="label label-success">BACKEND</span>
                            </div>
                        </div>
                        <div class="panel-body">
                            <p>
                                The <a href="{{ url('/tests') }}" target="_blank">Tests</a> section is created and it
                                will contain all your saved tests ready to become your student's exams.
                            </p>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Segments Logic &middot; <small class="text-warning">11 November 2017</small>
                            <div class="pull-right">
                                <span class="label label-primary">FRONTEND</span>
                                <span class="label label-success">BACKEND</span>
                            </div>
                        </div>
                        <div class="panel-body">
                            <p>
                                The <a href="{{ url('/segments') }}" target="_blank">Segments</a> section basic
                                functionality is ready with its actions and its full backend implimentation with the 2
                                first types of questions, Multiple and SIngle Choice.
                            </p>
                            <p>
                                <b>Functionalities added:</b> Listing, Create, Edit and Delete
                            </p>
                            <p>
                                <b>Functionalities comming soon:</b> Validation in Forms.
                            </p>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Lessons Logic &middot; <small class="text-warning">23 August 2017</small>
                            <div class="pull-right">
                                <span class="label label-primary">FRONTEND</span>
                                <span class="label label-success">BACKEND</span>
                            </div>
                        </div>
                        <div class="panel-body">
                            <p>
                                A new Tab <a href="{{ url('/lessons') }}" target="_blank">Lessons</a> is added to the
                                Navigation Bar, along with its
                                own basic listing functionalities.
                            </p>
                            <p>
                                <b>Functionalities added:</b> UI Tables, Dropdown with working FIlters and optional
                                Pagination
                            </p>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            First Blood &middot; <small class="text-warning">7 August 2017</small>
                            <div class="pull-right">
                                <span class="label label-success">BACKEND</span>
                            </div>
                        </div>
                        <div class="panel-body">
                            <b>Exams</b> after some time of reconsider, decided to start things from the scratch, for
                            more stuff up its back.
                            So here we go with <a href="https://laravel.com/docs/5.4" target="_blank">Laravel 5.4</a>
                            for our framework.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
