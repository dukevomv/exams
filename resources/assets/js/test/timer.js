
testUtils.initiateTimer = function() {
  $('.test-timer-wrap').removeClass('hidden');
  testUtils.setTimerTo(testData.timer.remaining_seconds);
  //dont reload if test havent finished auto
  if (testData.timer.in_delay) ;
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
  testUtils.setTimerTo(testData.timer.start_delay_in_seconds)
  testData.timer.running = true
  realtime.reloadOn(testData.timer.start_delay_in_seconds);
  if (testData.user.role == 'student' && !testData.test.user_on_test)
    window.location.reload;
});

realtime.on('test.finished', function (payload) {
  testUtils.setTimerTo(testData.timer.finish_delay_in_seconds)
  testData.timer.running = true
  realtime.reloadOn(testData.timer.finish_delay_in_seconds);
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
  if (testData.timer.in_delay)
    $('#test-timer').addClass('alarm');
  else
    $('#test-timer').removeClass('alarm');
}