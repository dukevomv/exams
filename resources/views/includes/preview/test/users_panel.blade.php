<div class="panel panel-default" id="test-registered-students">
    <table class="table">
        <tr>
            <th>Student Name</th>
            <th>Entered At</th>
            <th>Status</th>
            <th>Grade</th>
            <th class="text-center">Action</th>
        </tr>
        @foreach($users as $user)
            {{--                  todo make this dynamic with firebase and trust only db data--}}
            <tr>
                <td>{{$user['name']}}</td>
                <td>{{$user['entered_at']}}</td>
                <td>{{ucfirst($user['status'])}}</td>
                <td>@if(is_null($user['given_points']) && is_null($user['total_points'])) - @else {{$user['given_points']}}/{{$user['total_points']}} @endif</td>
                <th class="text-center">
                    <a href="{{url('/tests/'.$testId.'/users/'.$user['id'])}}" type="button"
                       class="btn btn-xs btn-primary">
                        <i class="fa fa-eye"></i>
                    </a>
                </th>
            </tr>
        @endforeach
    </table>
</div>