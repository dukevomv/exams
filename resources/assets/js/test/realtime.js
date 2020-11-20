testUtils.initializeRealtime = function(){
  realtime.init(testUtils.getTestData)
}

testUtils.getTestData = function() {
  var testsRef = firebase.database().ref('tests/' + testData.test.id);

  var eventAliases = [
    'test.started',
    'test.finished',

    'student.registered',
    'student.left',
  ];

  if (userData && userData.role == 'professor') {
    var studentsRef = firebase.database().ref('tests/' + testData.test.id + '/students');

    studentsRef.on('child_added', function (data) {
      var student = data.val();
      realtime.executeEvent('student.registered', {
        id: data.key,
        name: student.name,
        registered_at: student.registered_at
      });
    });

    studentsRef.on('child_removed', function (data) {
      var student = data.val();
      realtime.executeEvent('student.left', {
        id: data.key,
        name: student.name,
        registered_at: student.registered_at
      });
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