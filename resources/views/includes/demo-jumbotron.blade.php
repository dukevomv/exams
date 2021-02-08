@if(config('app.demo.enabled') && !Session::has('demo'))
    <div class="jumbotron">
        <h1>DEMO Mode</h1>
        <p>The current environment can be used as <b>DEMO</b> mode. <br>You can generate some <b>test data</b> in
            order to try the platform and it's features.</p>
        After generating your demo session
        <ul class="text-lg">
            <li>Users of all roles will be created and approved: <b>Admin, Professor, Student</b>.</li>
            <li>A <b>Lesson</b> will be created with Student and Professor registered on it.</li>
            <li>A <b>Draft test</b> created by Professor with no students attached on it.</li>
            <li>A <b>Scheduled test</b> created by Professor with Student registered on it.</li>
            <li>A <b>Started test</b> created by Professor with Student currently taking it.</li>
            <li>A <b>Started expired test</b> created by Professor with Student currently taking it with count-down set to 0..</li>
            <li>A <b>just finished test</b> created by Professor with Student answers still available for saving.</li>
            <li>A <b>Finished test</b> created by Professor with Student answers pending for Professor grading.</li>
            <li>2 <b>Graded tests</b> created by Professor with Student graded answers.</li>
            <li><b>34 more students</b> will be created and attached to the tests below with auto generated answers.</li>
            <li>You will be able to switch user roles from the <b>top right user dropdown</b>.</li>
        </ul>
        <br>
        <p>
            <form action="{{ url('/demo/generate') }}" method="POST">
                <input type="hidden" value="{{ csrf_token() }}" name="_token">
                <input type="email" name="email" class="form-control input-lg" placeholder="Your Email" required>
                <br>
                <button type="submit" class="btn btn-info btn-lg" role="button">Generate DEMO data
                </button>
            </form>
        </p>
    </div>
@endif