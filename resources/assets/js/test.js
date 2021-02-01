window.testData = {
  test: null,
  timer: null,
  user: null,
  now: null,
  serverSecondsDifference: null,
  clockInterval: null,
  taskData: {}
}

if(!window.testUtils)
  window.testUtils = {}

$('input.task-grade-points').on('change',function() {
  $(this).siblings('.input-group-btn').find('button').removeClass('btn-default').addClass('btn-primary')
});

$('.wrap-for-banners').addClass('col-xs-7');

require('./includes/lib/realtime');

require('./test/realtime');
require('./test/users');
require('./test/timer');
require('./test/toolbar');

require('./test/task_types/corerspondence');