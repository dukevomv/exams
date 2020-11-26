/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 330);
/******/ })
/************************************************************************/
/******/ ({

/***/ 330:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(331);


/***/ }),

/***/ 331:
/***/ (function(module, exports, __webpack_require__) {

window.testData = {
  test: null,
  timer: null,
  now: null,
  serverSecondsDifference: null,
  clockInterval: null,
  taskData: {}
};

window.testUtils = {};

__webpack_require__(332);

__webpack_require__(333);
__webpack_require__(334);
__webpack_require__(335);
__webpack_require__(336);

__webpack_require__(337);

/***/ }),

/***/ 332:
/***/ (function(module, exports, __webpack_require__) {

var config = {
  apiKey: "AIzaSyAkQaAVcCMajMmiqZUXJoeqjkktfgUzBHU",
  authDomain: "exams-p11018.firebaseapp.com",
  databaseURL: "https://exams-p11018.firebaseio.com",
  projectId: "exams-p11018",
  storageBucket: "exams-p11018.appspot.com",
  messagingSenderId: Object({"MIX_FIREBASE_API_KEY":"AIzaSyAkQaAVcCMajMmiqZUXJoeqjkktfgUzBHU","MIX_FIREBASE_AUTH_DOMAIN":"exams-p11018.firebaseapp.com","MIX_FIREBASE_DATABASE_URL":"https://exams-p11018.firebaseio.com","MIX_FIREBASE_PROJECT_ID":"exams-p11018","MIX_FIREBASE_STORAGE_BUCKET":"exams-p11018.appspot.com","NODE_ENV":"development"}).MESSAGING_SENDER_ID
};

var _events = {};
window.realtime = {
  init: function init(callback) {
    // Initialize Firebase
    firebase.initializeApp(config);
    firebase.auth().signInAnonymously().catch(function (error) {
      // Handle Errors here.
      var errorCode = error.code;
      var errorMessage = error.message;
      // ...
    });
    firebase.auth().onAuthStateChanged(function (user) {
      if (user) {
        callback(user);
      } else {
        // User is signed out.
      }
    });
  },
  executeEvent: function executeEvent(alias, payload) {
    if (_events[alias]) _events[alias](payload);else console.error('Event ' + alias + ' was not handled.');
  },
  on: function on(alias, func) {
    _events[alias] = func;
  },
  reloadOn: function reloadOn() {
    var seconds = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

    if (seconds) setTimeout(window.location.reload.bind(window.location), seconds * 1000);
  }
};

/***/ }),

/***/ 333:
/***/ (function(module, exports) {

testUtils.initializeRealtime = function () {
  realtime.init(testUtils.getTestData);
};

testUtils.getTestData = function () {
  var testsRef = firebase.database().ref('tests/' + testData.test.id);

  var eventAliases = ['test.started', 'test.finished', 'student.registered', 'student.left'];

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
};

/***/ }),

/***/ 334:
/***/ (function(module, exports) {

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

/***/ }),

/***/ 335:
/***/ (function(module, exports) {


testUtils.initiateTimer = function () {
  $('.test-timer-wrap').removeClass('hidden');
  testUtils.setTimerTo(testData.timer.remaining_seconds);
  //dont reload if test havent finished auto
  if (!testData.timer.actual_time) ;
  realtime.reloadOn(testData.timer.remaining_seconds);

  testData.clockInterval = setInterval(function () {
    if (testData.timer.running) if (testData.timer.remaining_seconds > 0) testUtils.setTimerTo(--testData.timer.remaining_seconds);
    if (!testData.test.can_register && moment().add(testData.server_diff, 'seconds').isAfter(testData.test.register_time)) {
      testData.test.can_register = true;
      $('#test-register').prop('disabled', false);
    }
  }, 1000);
};

realtime.on('test.started', function (payload) {
  testUtils.setTimerTo(testData.timer.seconds_gap);
  testData.timer.running = true;
  realtime.reloadOn(testData.timer.seconds_gap);
  if (testData.user.role == 'student' && !testData.test.user_on_test) window.location.reload;
});

realtime.on('test.finished', function (payload) {
  testUtils.setTimerTo(testData.timer.seconds_gap);
  testData.timer.running = true;
  realtime.reloadOn(testData.timer.seconds_gap);
});

testUtils.setTimerTo = function (seconds) {
  testData.timer.remaining_seconds = seconds;
  var minutes = Math.floor(seconds / 60);
  var hours = Math.floor(minutes / 60);
  var minutes = minutes % 60;
  var seconds_left = seconds % 60;
  var now = '';
  now = (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes + ':' + (seconds_left < 10 ? '0' : '') + seconds_left;
  $('#test-timer').text(now);
  if (testData.timer.actual_time) $('#test-timer').removeClass('alarm');else $('#test-timer').addClass('alarm');
};

/***/ }),

/***/ 336:
/***/ (function(module, exports) {

var testsURL = baseURL + '/tests/';

$('body').scrollspy({ target: '#segment-list' });

$('.task-value').on('change', function () {
  testUtils.toggleButton($('#save-test'), 'enable');
  testUtils.toggleButton($('#save-draft-test'), 'enable');
});

$('#start-test').on('click', function (e) {
  $.post(testsURL + testData.test.id + '/' + 'start', { _token: CSRF }, function () {
    $('#start-test').removeClass('btn-success').addClass('btn-default').prop('disabled', false);
  });
});
$('#finish-test').on('click', function (e) {
  $.post(testsURL + testData.test.id + '/' + 'finish', { _token: CSRF }, function () {
    $('#finish-test').removeClass('btn-danger').addClass('btn-default').prop('disabled', false);
  });
});

//todo these are not working
// - saving test and reloading
$('#save-test').on('click', function (e) {
  testUtils.saveTest(true);
});
$('#save-draft-test').on('click', function (e) {
  testUtils.saveTest();
});

testUtils.toggleButton = function (button, action) {
  var title = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';

  switch (action) {
    case 'disable':
      button.prop('disabled', true);
      button.addClass('btn-default');
      break;
    case 'enable':
      button.prop('disabled', false);
      button.removeClass('btn-default');
      break;
    default:
    //code
  }
  if (title !== '') button.text(title);
};

testUtils.saveTest = function () {
  var final = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

  var answers = [];

  $("#test-student-segments .task-wrap").each(function (index) {
    var task_type = $(this).attr('data-task-type');
    answers.push(GetTaskAnswers($(this), task_type));
  });

  $.post(testsURL + testData.test.id + '/' + 'submit', { final: final ? 1 : 0, answers: answers, _token: CSRF }, function () {
    testUtils.toggleButton($('#save-test'), 'enable', 'Submit' + (final ? '' : ' (1)'));
    testUtils.toggleButton($('#save-draft-test'), 'disable');

    if (final) {
      testUtils.toggleButton($('#save-test'), 'disable');
    }
  });

  function GetDOMValue(element) {
    var data = {};
    element.find('.task-value').each(function (i) {
      if ($(this).attr('data-value-prop')) {
        if ($(this).attr('data-value-prop') == 'checked') {
          data[$(this).attr('data-key')] = $(this).is(":checked") ? 1 : 0;
        }
      } else if ($(this).attr('data-value')) {
        data[$(this).attr('data-key')] = $(this).attr('data-value');
      }
    });
    return data;
  }

  function GetTaskAnswers(element, task_type) {
    var task = {
      id: element.attr('data-task-id'),
      type: task_type
    };
    switch (task_type) {
      case "rmc":
      case "cmc":
        task.data = [];
        element.find('.task-list .task-choice').each(function (i) {
          var choice = GetDOMValue($(this));
          task.data.push(choice);
        });
        break;
      case "free_text":
        task.data = element.find('textarea').val();
        break;
      case "correspondence":
        console.log('11112121');
        task.data = [];
        element.find('.choice-wrap').each(function (i) {
          var choice = {
            side_a: $(this).find('input.side-a').val(),
            side_b: $(this).find('input.side-b').val()
          };
          if (choice.side_a != '' && choice.side_b != '') task.data.push(choice);
        });
        break;
      case "code":
        //todo: fix code task type input
        task.data.push({
          id: element.find('.task-code input').val(),
          description: element.find('.task-code textarea').val()
        });
        break;
      default:
      //code block
    }
    return task;
  }
};

/***/ }),

/***/ 337:
/***/ (function(module, exports) {

var taskCorrespondenceWrap = ".task-wrap.task-wrap-correspondence";
var taskHandlerIdPrefix = taskCorrespondenceWrap + "#panel-task-";

function getCorrespondenceTaskAnswersAndElements(taskId) {
  var task = $(taskHandlerIdPrefix + taskId);
  var answers = {};
  task.find('.choice-wrap').each(function () {
    answers[$(this).find('input.side-a').val()] = {
      value: $(this).find('input.side-b').val(),
      element: $(this).find('input.side-b')
    };
  });
  return answers;
}

$(".choice-side-b a").click(function (e) {
  e.preventDefault();
  var sideB = $(this).text();
  var parent = $(this).closest('.input-group');
  var sideA = parent.find('input.side-a').val();
  var taskId = parent.closest('.task-wrap').attr('data-task-id');

  var taskAnswersAndElements = getCorrespondenceTaskAnswersAndElements(taskId);
  var taskAnswers = {};
  Object.keys(taskAnswersAndElements).forEach(function (a) {
    if (taskAnswersAndElements[a].value === sideB) {
      taskAnswersAndElements[a].element.val('');
    }
    taskAnswers[a] = taskAnswersAndElements[a].value;
  });
  parent.find('input.side-b').val(sideB);
  taskAnswers[sideA] = sideB;

  fixSelectedOptionsInDropdown(taskId, taskAnswers);

  //enable save buttons on change
  testUtils.toggleButton($('#save-test'), 'enable');
  testUtils.toggleButton($('#save-draft-test'), 'enable');
});

$(taskCorrespondenceWrap).each(function () {
  var taskId = $(this).attr('data-task-id');
  var taskAnswersAndElements = getCorrespondenceTaskAnswersAndElements(taskId);
  var taskAnswers = {};
  Object.keys(taskAnswersAndElements).forEach(function (a) {
    taskAnswers[a] = taskAnswersAndElements[a].value;
  });
  fixSelectedOptionsInDropdown(taskId, taskAnswers);
});

function fixSelectedOptionsInDropdown(taskId, taskAnswers) {
  $(taskHandlerIdPrefix + taskId + ' .panel-body .choice-wrap').each(function () {
    var valueB = $(this).find('input.side-b').val();

    var choiceIsEmpty = valueB === '';

    $(this).find('input.side-b').val(choiceIsEmpty ? '' : valueB);
    $(this).find('button.choice-button .text-overflow').text(choiceIsEmpty ? 'Option' : valueB);
    if (choiceIsEmpty) {
      $(this).find('button.choice-button').removeClass('btn-primary').addClass('btn-default');
    } else {
      $(this).find('button.choice-button').removeClass('btn-default').addClass('btn-primary');
    }

    var selected = Object.values(taskAnswers);
    $(this).find('.choice-side-b').removeClass('active').removeClass('selected').each(function () {
      var choiceB = $(this).find('a').text();
      if (valueB === choiceB) {
        $(this).addClass('active');
      } else if (selected.indexOf(choiceB) > -1) {
        $(this).addClass('selected');
      }
    });
  });
}

/***/ })

/******/ });