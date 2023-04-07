@component('mail::message')
    # A new DEMO was created

    Email: {{$demo_email}}

    Thanks,
    {{ config('app.name') }}
@endcomponent