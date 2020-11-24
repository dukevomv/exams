@foreach($task['choices']['a'] as $a)
    <div class="col-xs-12 row-margin-bottom choice-wrap">
        <div class="input-group col-xs-12 pull-left">
            <input type="hidden" class="form-control side-a text-wrap" value="{{$a}}">
            <input type="hidden" class="form-control side-b text-wrap" value="{{$task['answers'][$a]}}">
            <span class="form-control text-wrap choice-side-a">{{$a}}</span>
            <span class="input-group-addon"><i class="fa fa-hand-o-right"></i></span>
            <div class="input-group-btn">
                <button type="button" class="btn btn-default dropdown-toggle choice-button"
                        data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    <span class="no-padding col-xs-10 text-overflow">Option</span> <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    @foreach($task['choices']['b'] as $b)
                        <li class="choice-side-b"><a href="#">{{$b}}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endforeach