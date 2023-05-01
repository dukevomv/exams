@component('mail::message')
    # A new trial was created

    UUID: {{$trial->uuid}}
    Email: {{$trial->email}}
    Course Name: {{$trial->details['course_name']}}
    Scheduled At: {{$trial->details['scheduled_at']}}
    Duration: {{$trial->details['duration_in_minutes']}} minutes
    Reason: {{$trial->details['reason']}}

    Thanks,
    {{ config('app.name') }}
@endcomponent