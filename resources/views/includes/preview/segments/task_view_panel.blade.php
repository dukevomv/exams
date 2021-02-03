<div class="panel panel-default task-wrap task-wrap-{{$task['type']}} relative" id="panel-task-{{$task['id']}}"
     data-task-type="{{$task['type']}}" data-task-id="{{$task['id']}}">
    <div class="panel-heading">
        {{$task['description']}}
        @php
            $testData = [];
            if(isset($test_id)){
                $testData['test_id'] = $test_id;
            }
            if(isset($student_id)){
                $testData['student_id'] = $student_id;
            }
        @endphp
        @include('includes.preview.segments.task_points', array_merge($testData,[
            'calculative' => $task['calculative'],
            'manually_saved' =>  array_key_exists('manually_saved',$task) ? $task['manually_saved'] : false,
            'task_id' => $task['id'],
            'given' => array_key_exists('given_points',$task) ? $task['given_points'] : null,
            'total' => $task['points']
        ]))
    </div>
    <div class="panel-body">
        @include('includes.preview.segments.task_types.'.$task['type'], ['task' => $task])
    </div>
</div>