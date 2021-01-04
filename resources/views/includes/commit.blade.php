<div class="panel panel-default">
    <div class="panel-heading">
        {{$title}} &middot; <small class="text-warning">{{$date}}</small>
        <div class="pull-right">
            @php
                $tagMap = [
                    'front' => ['class' => 'primary','text'=>'FRONTEND'],
                    'back' => ['class' => 'success','text'=>'BACKEND'],
                    'progress' => ['class' => 'default','text'=>'IN PROGRESS'],
                    'new' => ['class' => 'danger','text'=>'NEW'],
                ];
            @endphp
            @foreach($tags as $tag)
                <span class="label label-{{$tagMap[$tag]['class']}}">{{$tagMap[$tag]['text']}}</span>
            @endforeach
        </div>
    </div>
    <div class="panel-body">
        {!! $body !!}
    </div>
</div>