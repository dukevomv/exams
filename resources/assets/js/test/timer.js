
testUtils.initiateTimer = function() {
  $('.test-timer-wrap').removeClass('hidden');
  testUtils.setTimerTo(testData.timer.remaining_seconds);
  //dont reload if test havent finished auto
  if (!testData.timer.actual_time) ;
    realtime.reloadOn(testData.timer.remaining_seconds);

  testData.clockInterval = setInterval(function () {
    if (testData.timer.running)
      if (testData.timer.remaining_seconds > 0)
        testUtils.setTimerTo(--testData.timer.remaining_seconds);
    if (!testData.test.can_register && moment().add(testData.server_diff, 'seconds').isAfter(testData.test.register_time)) {
      testData.test.can_register = true;
      $('#test-register').prop('disabled', false);
    }
  }, 1000);
}

realtime.on('test.started', function (payload) {
  testUtils.setTimerTo(testData.timer.seconds_gap)
  testData.timer.running = true
  realtime.reloadOn(testData.timer.seconds_gap);
  if (testData.user.role == 'student' && !testData.test.user_on_test)
    window.location.reload;
});

realtime.on('test.finished', function (payload) {
  testUtils.setTimerTo(testData.timer.seconds_gap)
  testData.timer.running = true
  realtime.reloadOn(testData.timer.seconds_gap);
});

testUtils.setTimerTo = function(seconds) {
  testData.timer.remaining_seconds = seconds;
  var minutes = Math.floor(seconds / 60);
  var hours = Math.floor(minutes / 60);
  var minutes = minutes % 60;
  var seconds_left = seconds % 60;
  var now = '';
  now = (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes + ':' + (seconds_left < 10 ? '0' : '') + seconds_left
  $('#test-timer').text(now);
  if (testData.timer.actual_time)
    $('#test-timer').removeClass('alarm');
  else
    $('#test-timer').addClass('alarm');
}