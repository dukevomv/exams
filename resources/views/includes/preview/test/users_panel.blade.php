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
            <tr data-id="{{$user['id']}}" id="student-{{$user['id']}}">
                <td>{{$user['name']}}</td>
                <td>{{$user['entered_at']}}</td>
                <td><span class="label label-{{$user['status']}}">{{ucfirst($user['status'])}}</span></td>
                <td>@if(is_null($user['given_points']) && is_null($user['total_points'])) - @else {{\App\Util\Points::getWithPercentage($user['given_points'],$user['total_points'])}} @endif</td>
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

<script>


  function jsUcfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }
    if(!window.testUtils)
        window.testUtils = {};

    testUtils.buildStudentRowAttributes = function(studentId){
      return 'data-id="' + studentId + '" id="student-' + studentId + '"';
    }
    testUtils.buildStudentRowColumns = function(id,name,entered_at,status,grades='-'){
      return '<td>' + name + '</td>\
              <td>' + entered_at + '</td>\
              <td><span class="label label-'+status+'">'+jsUcfirst(status)+'</span></td>\
              <td>'+grades+'</td>\
              <th class="text-center">\
                    <a href="'+baseURL+'/tests/'+testData.id+'/users/'+id+'" type="button"\
                       class="btn btn-xs btn-primary">\
                        <i class="fa fa-eye"></i>\
                    </a>\
                </th>';
    }


</script>