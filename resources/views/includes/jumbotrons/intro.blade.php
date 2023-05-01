@if(config('app.demo.enabled'))
    <div class="col-xs-12 trial-demo-wrap">
        <div class="row">
            <div class="col-xs-12 col-md-6">
                @include('includes.jumbotrons.demo_intro')
            </div>
            <div class="col-xs-12 col-md-6">
                @include('includes.jumbotrons.trial_intro')
            </div>
        </div>
    </div>
@endif