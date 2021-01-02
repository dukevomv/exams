window.testData = {
  test: null,
  timer: null,
  user: null,
  now: null,
  serverSecondsDifference: null,
  clockInterval: null,
  taskData: {}
}

window.testUtils = {}

$('input.task-grade-points').on('change',function() {
  $(this).siblings('.input-group-btn').find('button').removeClass('btn-default').addClass('btn-primary')
});

require('./includes/lib/realtime');

require('./test/realtime');
require('./test/users');
require('./test/timer');
require('./test/toolbar');

require('./test/task_types/corerspondence');