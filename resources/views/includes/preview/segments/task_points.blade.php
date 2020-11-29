@php
    $ratio = (isset($given) && isset($total) && $total > 0) ? $given/$total : 0;
    $class = 'default';

    if($ratio == 0){
        $class = 'danger';
    } elseif($ratio <= 0.5){
        $class = 'warning';
    } elseif($ratio > 0.5){
        $class = 'success';
    }

    $output = $total. ' pts';
    if(isset($given)){
        $output = $given.'/'.$output;
    }
@endphp

<span class="pull-right label label-{{$class}}">{{$output}}</span>