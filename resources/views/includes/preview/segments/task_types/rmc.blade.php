@php
    $rmc_name = 'unique-rmc-' . $task->id;
@endphp

<div class="task-list">
    @foreach($task->rmc as $choice)
        <div class="col-md-12 task-choice row-margin-bottom">
            <label class="cursor-pointer">
                <span class="task-value" data-value="{{$choice->id}}" data-key="id"></span>
                <input type="radio" class="task-value" id="task-{{$task->id}}-choice-{{$choice->id}}" data-key="correct"
                       data-value-prop="checked" @if($choice->selected) checked
                       @endif class="autosave-field choice-correct set-unique-val" name="{{$rmc_name}}">
                <span>{{$choice->description}}</span>
            </label>
            @include('includes.preview.segments.task_answer', ['value' =>$choice->given_points,'correct' => ($choice->correct == $choice->selected)])
        </div>
    @endforeach
</div>