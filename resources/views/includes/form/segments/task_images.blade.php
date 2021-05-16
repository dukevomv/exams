<div class="col-xs-12 row-margin-bottom task-images">
    <label><i class="fa fa-picture-o" aria-hidden="true"></i> Task Images:</label>
    <div class="image-wrap">
        @foreach($task->images as $img)
            <div class="image-holder no-padding col-xs-12">
                <form method="POST" action="{{ url('tasks/'.$task->id.'/images/'.$img->id) }}">
                    {{csrf_field()}}
                    <div class="input-group col-xs-11 pull-left">
                        <span class="input-group-btn">
    {{--                        <button class="btn btn-default"><i class="fa fa-arrows"></i></button>--}}
                            <button class="btn btn-default" data-toggle="modal" data-title="{{$img->title}}" data-src="{{$img->url}}" data-target="#image-modal"><i class="fa fa-eye"></i></button>
                        </span>
                        <input type="text" name="title" class="form-control" value="{{$img->title}}">
                        <span class="input-group-btn">
                            <button class="btn btn-primary"><i class="fa fa-save"></i></button>
                        </span>
                    </div>
                </form>
                <form method="POST" action="{{ url('tasks/'.$task->id.'/images/'.$img->id.'/remove') }}">
                    {{csrf_field()}}
                    <button class="btn btn-link"><i class="fa fa-minus-circle text-danger"></i></button>
                </form>
            </div>
        @endforeach
    </div>
    <div class="upload-wrap col-xs-12 no-padding">
        <form method="POST" action="{{ url('tasks/'.$task->id.'/images') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input class="col-xs-10 no-padding" type="file" name="images[]" multiple placeholder="Add Images">
            <button class="col-xs-2 no-padding btn btn-xs btn-primary" type="submit">Upload</button>
            <i class="col-xs-12 no-padding">File names will be used as image titles in order to use in your task descriptions.<br> You will be able to change it later on.</i>
        </form>
    </div>
</div>