@component('mail::message')
    # Your OTP was generated: {{$code}}

    The code above will expire in 5 minutes.

    Thanks,
    {{ config('app.name') }}
@endcomponent