<div class="task-list">
    @foreach($task['choices'] as $choice)
        <div class="col-md-12 task-choice row-margin-bottom">
            <label class="cursor-pointer">
                <span class="task-value" data-value="{{$choice['id']}}" data-key="id"></span>
                <input type="checkbox" class="task-value" data-key="correct" data-value-prop="checked"
                       id="task-{{$task['id']}}-choice-{{$choice['id']}}"
                       @if(array_key_exists('selected',$choice) && $choice['selected']) checked
                       @endif class="autosave-field choice-correct set-unique-val">
                <span>{{$choice['description']}}</span>
            </label>
            @if(array_key_exists('selected',$choice) && array_key_exists('correct',$choice))
                @include('includes.preview.segments.task_answer', ['value' =>$choice['given_points'],'correct' => ($choice['correct'] == $choice['selected'])])
            @endif
        </div>
    @endforeach
</div>