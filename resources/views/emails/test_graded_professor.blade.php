@component('mail::message')
# {{$test['name']}} was graded

## Examination Statistics
@if(isset($test['stats']))
<span>Participated: <b>{{$test['stats']['students']['participated']}}/{{$test['stats']['students']['total']}} students</b></span>
<br>
<span>Above 50%: <b>{{$test['stats']['students']['passed']}} students</b></span>
<br>
<br>
<span>Graded: <b>{{$test['stats']['students']['graded']}}/{{$test['stats']['students']['total']}} students</b></span>
<br>
<span>Minimum: <b>{{$test['stats']['min']}}</b></span>
<br>
<span>Maximum: <b>{{$test['stats']['max']}}</b></span>
<br>
<span>Range: <b>{{$test['stats']['range']}}</b></span>
<br>
<span>Average: <b>{{$test['stats']['average']}}</b></span>
<br>
<span>Standard Deviation: <b>{{$test['stats']['standard_deviation']}}</b></span>
@endif

We have also attached each student's grade with percentage in a csv format.

Thanks,
{{ config('app.name') }}
@endcomponent