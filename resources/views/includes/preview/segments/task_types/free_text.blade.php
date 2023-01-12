<textarea class="col-md-12 task-value" data-key="correct" data-value-prop="textarea" data-input-label="answer"
          rows="7">{{array_key_exists('answer',$task) ? $task['answer'] : ""}}</textarea>
@if(array_key_exists('description',$task) && \Auth::user()->role == \App\Enums\UserRole::PROFESSOR)
  <div class="col-xs-12">
      <a class="btn btn-primary" role="button" data-toggle="collapse" href="#answer-collapse-{{$task['answer']}}" aria-expanded="false" aria-controls="answer-collapse-{{$task['answer']}}">
        <i class="fa fa-book"></i> Answer
      </a>
      <label><input type="checkbox" disabled @if($task['autocomplete'] ?? false) checked @endif> Autocorrect with exact match</label>
      <div class="collapse" id="answer-collapse-{{$task['answer']}}">
        <div class="well">
          {{$task['professor_answer'] ?? ''}}
        </div>
      </div>
  </div>
@endif
