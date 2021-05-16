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
                        <img class="col-xs-4 pull-right" src="{{URL::to('images/professor.png')}}" alt="professor-illustration">
                        <h1>Hi, {{Auth::user()->name}}</h1>
                        <p><span class="label label-danger">NEW</span><br>The Current feature is added in the latest
                            group of updates.</p>
                        <p><span class="label label-primary">FRONTEND</span><br>Most likely to contain User Interface
                            changes that will provide new flows and functionalities to the user.</p>
                        <p><span class="label label-success">BACKEND</span><br>Contains implementation in the background
                            that fixes the infrastructure and might not be visible to the user.</p>
                    </div>

                @php
                    $new_commits = [
                        [
                            'title' => 'Demo Data Exclusion',
                            'date' => '16 May 2021',
                            'tags' => ['back'],
                            'body' => '<p><b>Exclude</b> Demo data when user login normally.<br>Also <b>include</b> only current user\'s data when in demo mode.</p>'
                        ],
                        [
                            'title' => 'Image support',
                            'date' => '15 May 2021',
                            'tags' => ['back','front'],
                            'body' => '<p>Allow professors to include <b>images</b> in the segment tasks.</p>'
                        ],
                        [
                            'title' => 'Test Guidelines',
                            'date' => '13 May 2021',
                            'tags' => ['front'],
                            'body' => '<p><b>Explain the actions</b> each user can do during the examination time. Including student and professor\'s buttons</p>.'
                        ]
                    ];

                    for($i=0;$i<count($new_commits);$i++){
                        $new_commits[$i]['tags'][] = 'new';
                    }

                    $commits = array_merge($new_commits,[
                        [
                            'title' => 'Export to CSV',
                            'date' => '9 March 2021',
                            'tags' => ['back','front'],
                            'body' => '<p>Allow professors to <b>export</b> test grades to csv.</p>'
                        ],
                        [
                            'title' => 'Grading stats',
                            'date' => '7 March 2021',
                            'tags' => ['back','front'],
                            'body' => '<p>Calculate <b>grade range and standard deviation</b> on test pages - professor view only.</p>'
                        ],
                        [
                            'title' => 'Auto Grading Tests',
                            'date' => '8 February 2021',
                            'tags' => ['back','front'],
                            'body' => '<p>New actions have been added for the professor view in Test Lobby, providing the ability to <b>auto grade all test students</b> (when of course the test contains auto-calculative tasks).</p>'
                        ],
                        [
                            'title' => 'Segment Updates',
                            'date' => '3 February 2021',
                            'tags' => ['front'],
                            'body' => '<p>Show in professor\'s view a <b>warning</b> if the segment has changed after the test\'s publication.</p>'
                        ],
                        [
                            'title' => 'Segment Re-usability',
                            'date' => '3 February 2021',
                            'tags' => ['back'],
                            'body' => '<p>Refactor backend logic to fetch data from cached test when published in order to <b>avoid changing an old test</b> by updating the segments used.</p>'
                        ],
                        [
                            'title' => 'One Time Passwords',
                            'date' => '19 January 2021',
                            'tags' => ['front','back'],
                            'body' => '<p>All users now can have <b>OTP</b> as a second step verification after they login.</p>'
                        ],
                        [
                            'title' => 'Test Grading',
                            'date' => '4 January 2021',
                            'tags' => ['front','back'],
                            'body' => '<p>Professors can <b>grade student answers</b> after the tests are finished, and then <b>publish the results</b> to the students.</p>'
                        ],
                        [
                            'title' => 'Auto Grading',
                            'date' => '3 January 2021',
                            'tags' => ['back'],
                            'body' => '<p>Some of the task types are now able to <b>automatically calculate student\'s points</b> based on their answers.</p>'
                        ],
                        [
                            'title' => 'Demo Helper',
                            'date' => '2 January 2021',
                            'tags' => ['front','back'],
                            'body' => '<p>Specific environments can register users with the Demo seeder, providing a <b>helpful UI</b> to easily see the features of this platforms <b>without creating multiple accounts</b>.</p>'
                        ],
                        [
                            'title' => 'Automated Tests',
                            'date' => '13 December 2020',
                            'tags' => ['back'],
                            'body' => '<p>In order to ensure the quality of the project\'s features and utilities, an <b>automated test suite has been developed</b> and maintained as features are added.</p>'
                        ],
                        [
                            'title' => 'Task Type answers',
                            'date' => '27 November 2020',
                            'tags' => ['front','back'],
                            'body' => '<p>Now <b>Multiple choice (Radio & Checkbox), Correspondence and Free text</b> questions are operating normally during the test\'s duration ans are savable from the students</p>'
                        ],
                        [
                            'title' => 'Task Type Correspondence',
                            'date' => '27 November 2020',
                            'tags' => ['front','back'],
                            'body' => '<p>A new task type was added that is named <b>Correspondence</b>. It provides the ability to match pairs.</p>'
                        ],
                        [
                            'title' => 'User Administration',
                            'date' => '28 July 2018',
                            'tags' => ['front','back'],
                            'body' => '<p><b>Admins</b> can now manage <a href="'.url('/users').'" target="_blank">Users</a> by approving or disapproving current users.</p>'
                        ],
                        [
                            'title' => 'URL filters on Lists and Search',
                            'date' => '11 May 2018',
                            'tags' => ['front'],
                            'body' => '<p><b>Filters</b> are available to all lists <b>Users</b>, <b>Courses</b> and <b>Tests</b>inside url for permanent and sharable filtering.</p>
                                <p><b>Search</b> is also added to the lists and provides the ability to look into custom fields based on the list.</p>'
                        ],
                        [
                            'title' => 'Authorization',
                            'date' => '18 March 2018',
                            'tags' => ['back'],
                            'body' => '<p>
                                Authorization for specific role is filtered dynamically and shows error page based on
                                the action.
                            </p>
                            <p>
                                Links from other roles that dont show in Navigation Bar are also filtered.
                            </p>'
                        ],
                        [
                            'title' => 'Routing based on role',
                            'date' => '22 February 2018',
                            'tags' => ['front'],
                            'body' => '<p>
                                Tabs in the Navigation bar are different per <b>Role</b> and provide a Role hierarchy
                            </p>
                            <p>
                                Admins <b>></b> Professors <b>></b> Students
                            </p>'
                        ],
                        [
                            'title' => 'User registration based on type',
                            'date' => '04 February 2018',
                            'tags' => ['back','front'],
                            'body' => '<p>
                                The <a href="'.url('/register').'" target="_blank">Register</a> page, now has
                                <b>Role</b> option for Admin, Professors or Students.
                            </p>
                            <p>
                                All Users that register are saved in a <b>pending</b> state and must be approved by an
                                existing approved user in the platform.
                            </p>'
                        ],
                        [
                            'title' => 'Tests Logic',
                            'date' => '12 November 2017',
                            'tags' => ['back'],
                            'body' => '<p>
                                The <a href="'.url('/tests').'" target="_blank">Tests</a> section is created and it
                                will contain all your saved tests ready to become your student\'s exams.
                            </p>'
                        ],
                        [
                            'title' => 'Segments Logic',
                            'date' => '11 November 2017',
                            'tags' => ['front','back'],
                            'body' => '<p>
                                The <a href="'.url('/segments').'" target="_blank">Segments</a> section basic
                                functionality is ready with its actions and its full backend implementation with the 2
                                first types of questions, Multiple and Single Choice.
                            </p>
                            <p>
                                <b>Functionalities added:</b> Listing, Create, Edit and Delete
                            </p>
                            <p>
                                <b>Functionalities coming soon:</b> Validation in Forms.
                            </p>'
                        ],
                        [
                            'title' => 'Courses Logic',
                            'date' => '23 August 2017',
                            'tags' => ['front','back'],
                            'body' => '<p>
                                A new Tab <a href="'.url('/lessons').'" target="_blank">Courses</a> is added to the
                                Navigation Bar, along with its
                                own basic listing functionalities.
                            </p>
                            <p>
                                <b>Functionalities added:</b> UI Tables, Dropdown with working Filters and optional
                                Pagination
                            </p>'
                        ],
                        [
                            'title' => 'First Blood',
                            'date' => '7 August 2017',
                            'tags' => ['back'],
                            'body' => '<p><b>Exams</b> after some time of reconsider, decided to start things from the scratch, for
                            more stuff up its back.
                            So here we go with <a href="https://laravel.com/docs/5.4" target="_blank">Laravel 5.4</a>
                            for our framework.</p>'
                        ],
                    ]);
               @endphp

                @foreach($commits as $commit)
                    @include('includes.commit',$commit)
                @endforeach

                @endif
            </div>
        </div>
    </div>
@endsection
