@component('mail::message')
# {{$test->name}} was graded

You made <b>{{\App\Util\Points::getWithPercentage($student['given_points'],$student['total_points'])}}</b>.

Thank you for using {{ config('app.name') }}
@endcomponent