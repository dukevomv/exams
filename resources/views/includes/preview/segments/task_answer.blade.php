@php
    if(!isset($correct))
        $correct = false;

    $output = ($correct ? 'Correct' : 'Wrong');
    $class = ($correct ? 'success' : 'danger');
@endphp

<span class="pull-right text-{{$class}}">{{$output}}</span>