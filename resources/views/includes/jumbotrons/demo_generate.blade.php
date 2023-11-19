
<div class="jumbotron demo-wrap">
    <div class="row">
        @if(isset($position) && $position === 'reverse')
            <div class="image-wrap">
                <img width="150px" src="{{asset('images/stars@2x.png')}}" alt="">
            </div>
        @endif
        <div class="row">
            <div class="col-xs-8">
                <h3>Demo the platform</h3>
                <p>You can generate some <b>test data</b> in order to try the platform and it's features.</p>
            </div>
        </div>
        @if(!isset($position) || $position !== 'reverse')
            <div class="image-wrap">
                <img width="150px" src="{{asset('images/stars@2x.png')}}" alt="">
            </div>
        @endif
        <form action="{{url('/demo/generate')}}" method="POST">
            <input type="hidden" value="{{ csrf_token() }}" name="_token">
            <div class="g-recaptcha @if(!config('recaptcha.enabled')) hidden @endif" data-sitekey="{{config('recaptcha.key')}}"></div>
            <br>
            <div class="input-group search-wrap">
                <input type="email" name="demo_email" class="form-control input-lg" placeholder="Your Email" value="{{ old('demo_email') }}" required>
                <span class="input-group-btn">
                                        <button class="btn btn-primary btn-lg" type="submit">Generate Demo Data</button>
                                    </span>
            </div>
        </form>
    </div>
</div>