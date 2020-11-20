realtime.on('student.registered', function (student) {
  $("#test-registered-students .table").append('<tr data-id="' + student.id + '" class="student-' + student.id + '">\
          <td>' + student.name + '</td>\
          <td>' + student.registered_at + '</td>\
          <td><span class="label label-warning">Registered</span></td>\
          <td></td>\
          <td></td>\
        </tr>');
});

realtime.on('student.left', function (student) {
  $("#test-registered-students .table tr.student-" + student.id).remove();
});