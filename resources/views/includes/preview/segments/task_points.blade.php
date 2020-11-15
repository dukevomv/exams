@php
    $ratio = (isset($given) && isset($total)) ? $given/$total : null;
    $class = 'default';

    if(!is_null($ratio)){
        if($ratio == 0){
            $class = 'danger';
        } elseif($ratio <= 0.5){
            $class = 'warning';
        } elseif($ratio > 0.5){
            $class = 'success';
        }
    }
    $output = $total. ' pts';
    if(isset($given)){
        $output = $given.'/'.$output;
    }
@endphp

<span class="pull-right label label-{{$class}}">{{$output}}</span>