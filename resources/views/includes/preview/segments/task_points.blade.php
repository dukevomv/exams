@php
    $ratio = (isset($given) && !is_null($given) && isset($total) && !is_null($total) && $total > 0) ? $given/$total : null;
    $class = 'default';

    if(is_null($ratio)){
        $class = 'default';
    } elseif($ratio === 0){
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

$saveButtonClass = 'primary';
if($manually_saved){
    $saveButtonClass = 'default';
}

@endphp
<style>
    .editable {
        cursor: pointer;
    }
</style>

<div class="col-md-3 no-padding pull-right">
    @if($editable)
        <form method="POST" action="{{ url('/tests/'.$test_id.'/users/'.$student_id.'/grade-task')}}">
            {{ csrf_field() }}
            <input type="hidden" name="task_id" value="{{$task_id}}">
            <div class="input-group input-group-sm">
                <input type="number" class="form-control bg-success task-grade-points" name="points" value="{{$given}}" max="{{$total}}"
                       style="text-align: right;}">
                <span class="input-group-addon text-success" id="sizing-addon3"><b class="text-{{$class}}">/{{$total}} pts</b></span>
                <span class="input-group-btn">
                    <button class="btn btn-{{$saveButtonClass}}" type="submit"><i class="fa fa-save"></i></button>
                </span>

            </div>
        </form>
    @else
        <span class="pull-right label label-{{$class}} @if($editable) editable @endif">{{$output}}</span>
    @endif
</div>

{{-- todo show input  on click
hide input on click
on save update points and save on test
create function to use input values and generate pills and colors (not on load)
calculate automated given points once in back
and append the proffessors on top
keep data in db as well
--}}