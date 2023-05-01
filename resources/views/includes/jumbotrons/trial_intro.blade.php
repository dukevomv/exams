<div class="jumbotron trial-login-wrap">
    <div class="row">
        <div class="row">
            <div class="col-xs-8">
                <h3>Generate Trial Exam</h3>
                <p>Perform a real examination with student invites on a future date.<br>You will need to generate the Exam questions after setup.</p>
                <a href="{{url('trial')}}">Setup Trial Exam <i class="fa fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="image-wrap @if(isset($position) && $position === 'reverse') reverse @endif">
            <img width="100px" src="{{asset('images/professor.png')}}" alt="">
        </div>
    </div>
</div>