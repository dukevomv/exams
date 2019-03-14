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
/******/ 	return __webpack_require__(__webpack_require__.s = 166);
/******/ })
/************************************************************************/
/******/ ({

/***/ 166:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(167);


/***/ }),

/***/ 167:
/***/ (function(module, exports) {

realtime.init(getTestData);

function getTestData(firebaseUser) {
  var database = firebase.database();
  var testsRef = firebase.database().ref('tests/' + current.test.id);

  var eventAliases = ['test.started', 'test.finished', 'student.registered', 'student.left'];

  if (current.user.role == 'professor') {
    var studentsRef = firebase.database().ref('tests/' + current.test.id + '/students');

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
    if (current.test.status === 'published' && data.key === 'started_at') {
      current.test.status = 'started';
      realtime.executeEvent('test.started', data.val());
    }
    if (current.test.status === 'started' && data.key === 'finished_at') {
      current.test.status = 'finished';
      realtime.executeEvent('test.finished', data.val());
    }
  });
}

/***/ })

/******/ });