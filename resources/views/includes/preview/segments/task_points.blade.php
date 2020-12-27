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
if(Auth::user()->role == \App\Enums\UserRole::PROFESSOR && isset($test_id) && isset($student_id)){
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
        <form method="POST" action="{{ url('/tests/'.$test_id.'/users/'.$student_id.'/grade-task')}}">
            {{ csrf_field() }}
            <input type="hidden" name="task_id" value="{{$task_id}}">
            <input type="number" class="form-control" name="points" value="{{$given}}" max="{{$total}}">
            <div class="input-group-append pull-right">
                <button class="btn btn-primary btn-sm"><i class="fa fa-save"></i></button>
            </div>
        </form>
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