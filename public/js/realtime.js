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
/******/ 	return __webpack_require__(__webpack_require__.s = 169);
/******/ })
/************************************************************************/
/******/ ({

/***/ 169:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(170);


/***/ }),

/***/ 170:
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

/***/ })

/******/ });