@if(config('app.demo.enabled') && !Session::has('demo'))
    <div class="jumbotron">
        <h1>Trial Mode</h1>
        <p>Perform a live examination online. <br>Create a test for your class to take, and get <b>instant results</b>.</p>
        <ul class="text-lg">
            <li><b>Skip Signup flow</b></li>
            <li><b>No payments required</b></li>
            <li>You will need to provide the date and time of your test, along with the duration (up to 90 minutes).</li>
            <li>A <b>User</b> will be created with role Professor which will have the ability to start and finish the test.</li>
            <li>You will be able to create Segments that will be used in your test containing all the questions of the test.</li>
            <li>Then edit the exising test, and attach the segments you've created earlier.</li>
            <li>Create an email list with your students so you can invite them.</li>
            <li>By saving the test as published, you will allow your invite list to subscribe to your test, and participate</li>
            <li>After the test has finished you will be able to grade the results and get a full report email.</li>
            <li>We will also send an email to each student with their results.</li>
            <li>After 30 days, this account and it's data will be lost forever.</li>
        </ul>
        <p>
            <form action="{{ url('/trial/generate') }}" method="POST">
                <input type="hidden" value="{{ csrf_token() }}" name="_token">
                <div class="row">
                    <div class="col-xs-6">
                        <input type="email" name="email" class="form-control input-lg" placeholder="Your Email" required>
                    </div>
                    <div class="col-xs-6">
                        <input type="text" name="name" class="form-control input-lg" placeholder="Test Name" required>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-xs-6">
                        <input type="date" name="scheduled_at" class="form-control input-lg" placeholder="Examination Date" required>
                    </div>
                    <div class="col-xs-6">
                        <input type="number" name="duration_in_mins" class="form-control input-lg" placeholder="Duration (in mins)" required>
                    </div>
                </div>
                <br>
                        <input type="text" name="scheduled_at" class="form-control input-lg" placeholder="Reason of test (school, trial, friend dropdown)" required>
                <br>
                <button type="submit" class="btn btn-info btn-lg pull-right" role="button">Start Trial
                </button>
            </form>
                <br>
        </p>
    </div>
@endif