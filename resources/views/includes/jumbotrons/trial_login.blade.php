
<div class="jumbotron trial-login-wrap">
    <div class="row">
        @if(isset($position) && $position === 'reverse')
            <div class="image-wrap">
                <img width="100px" src="{{asset('images/professor.png')}}" alt="">
            </div>
        @endif
        <div class="row">
            <div class="col-xs-8">
                <h3>Find your Trial Exam</h3>
                <p>Add your email, and you'll receive a <br><b>One-Time Password</b> to your inbox and access your Trial Exam.</p>
            </div>
        </div>
        @if(!isset($position) || $position !== 'reverse')
            <div class="image-wrap">
                <img width="100px" src="{{asset('images/professor.png')}}" alt="">
            </div>
        @endif
        <form action="{{url('/trial/send-login-code')}}" method="POST">
            <input type="hidden" value="{{ csrf_token() }}" name="_token">
            <div class="input-group search-wrap">
                <input type="email" name="trial_email" class="form-control input-lg" placeholder="Your Email" value="{{ old('trial_email') }}" required>
                <span class="input-group-btn">
                                        <button class="btn btn-primary btn-lg" type="submit">Send Login Code</button>
                                    </span>
            </div>
        </form>
    </div>
</div>