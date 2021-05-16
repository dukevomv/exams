
@if(isset($task['images'])&& count($task['images']) > 0)
    <div class="col-xs-12 row-margin-bottom task-images">
        <label><i class="fa fa-picture-o" aria-hidden="true"></i> Task Images:</label>
        <div class="image-wrap">
            @foreach($task['images'] as $img)
                <div class="image-holder no-padding col-xs-12">
                    <div class="input-group col-xs-12 pull-left">
                        <button class="btn btn-default" data-toggle="modal" data-title="{{$img['title']}}" data-src="{{$img['url']}}" data-target="#image-modal"><i class="fa fa-eye"></i> {{$img['title']}}</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif