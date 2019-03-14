realtime.init(getTestData)

function getTestData(firebaseUser) {
  var database = firebase.database();
  var testsRef = firebase.database().ref('tests/' + current.test.id);
  
  var eventAliases = [
    'test.started',
    'test.finished',
    
    'student.registered',
    'student.left',
  ];
  
  if (current.user.role == 'professor') {
    var studentsRef = firebase.database().ref('tests/' + current.test.id + '/students');
    
    studentsRef.on('child_added', function(data) {
      var student = data.val();
      realtime.executeEvent('student.registered',{
        id : data.key,
        name : student.name,
        registered_at : student.registered_at
      });
    });
    
    studentsRef.on('child_removed', function(data) {
      var student = data.val();
      realtime.executeEvent('student.left',{
        id : data.key,
        name : student.name,
        registered_at : student.registered_at
      });
    });
  }
  
  testsRef.on('child_added', function(data) {
    if(current.test.status === 'published' && data.key === 'started_at'){
      current.test.status = 'started';
      realtime.executeEvent('test.started',data.val());
    }
    if(current.test.status === 'started' && data.key === 'finished_at'){
      current.test.status = 'finished';
      realtime.executeEvent('test.finished',data.val());
    }
  });
  
}