realtime.on('student.registered', function (student) {
  let table =  $("#test-registered-students .table");
  if(!table.find("#student-"+student.id)){
    table.append('<tr '+testUtils.buildStudentRowAttributes(student.id)+'></tr>')
  }
  let  studentRow = table.find("#student-"+student.id);
  studentRow.html(testUtils.buildStudentRowColumns(student.id,student.name,moment(student.registered_at).fromNow(),'registered'));
});

realtime.on('student.changed', function (student) {
  let table =  $("#test-registered-students .table");
  if(!table.find("#student-"+student.id)){
    table.append('<tr '+testUtils.buildStudentRowAttributes(student.id)+'></tr>')
  }
  let  studentRow = table.find("#student-"+student.id);
  studentRow.html(testUtils.buildStudentRowColumns(student.id,student.name,moment(student.registered_at).fromNow(),student.status));
});
//
// realtime.on('student.left', function (student) {
//   $("#test-registered-students .table tr.student-" + student.id).remove();
// });