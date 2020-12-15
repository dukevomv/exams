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

$editable = false;
if(Auth::user()->role == \App\Enums\UserRole::PROFESSOR){
    $editable = true;
}
@endphp
<style>
    .editable {
        cursor: pointer;
    }
</style>

<span class="pull-right label label-{{$class}} @if($editable) editable @endif">{{$output}}</span>
@if($editable)
    <div class="input-group mb-3 pull-right">
        <input type="number" class="form-control" value="{{$given}}" max="{{$total}}">
        <div class="input-group-append pull-right">
            <button class="btn btn-primary btn-sm"><i class="fa fa-save"></i></button>
        </div>
    </div>
@endif

{{--show input  on click
hide input on click
on save update points and save on test
create function to use input values and generate pills and colors (not on load)
calculate automated given points once in back
and append the proffessors on top
keep data in db as well
--}}