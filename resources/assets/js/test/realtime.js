testUtils.initializeRealtime = function(){
  realtime.init(testUtils.getTestData)
}

testUtils.getTestData = function() {
  var testsRef = firebase.database().ref('tests/' + testData.test.id);

  var eventAliases = [
    'test.started',
    'test.finished',

    'student.registered',
    'student.updated',
    'student.left',
  ];

  if (userData && userData.role == 'professor') {
    var studentsRef = firebase.database().ref('tests/' + testData.test.id + '/students');

    studentsRef.on('child_added', function (data) {
      triggerStudentChanged(data.key,data.val());
    });
    studentsRef.on('child_changed', function (data) {
      triggerStudentChanged(data.key,data.val());
    });

    function triggerStudentChanged(id,student){
      realtime.executeEvent('student.changed', {
        id: id,
        name: student.name,
        registered_at: student.registered_at,
        status: student.status
      });
    }


    studentsRef.on('child_removed', function (data) {
      triggerStudentChanged(data.key,data.val());
    });
  }

  testsRef.on('child_added', function (data) {
    if (testData.test.status === 'published' && data.key === 'started_at') {
      testData.test.status = 'started';
      realtime.executeEvent('test.started', data.val());
    }
    if (testData.test.status === 'started' && data.key === 'finished_at') {
      testData.test.status = 'finished';
      realtime.executeEvent('test.finished', data.val());
    }
  });

}