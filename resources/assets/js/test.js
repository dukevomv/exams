window.testData = {
  test: null,
  timer: null,
  now: null,
  serverSecondsDifference: null,
  clockInterval: null,
  taskData: {}
}

window.testUtils = {}

require('./includes/lib/realtime');

require('./test/realtime');
require('./test/users');
require('./test/timer');
require('./test/toolbar');

require('./test/task_types/corerspondence');