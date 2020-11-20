var config = {
  apiKey: process.env.MIX_FIREBASE_API_KEY,
  authDomain: process.env.MIX_FIREBASE_AUTH_DOMAIN,
  databaseURL: process.env.MIX_FIREBASE_DATABASE_URL,
  projectId: process.env.MIX_FIREBASE_PROJECT_ID,
  storageBucket: process.env.MIX_FIREBASE_STORAGE_BUCKET,
  messagingSenderId: process.env.MESSAGING_SENDER_ID
};

var _events = {};
window.realtime = {
  init : function(callback){
    // Initialize Firebase
    firebase.initializeApp(config);
    firebase.auth().signInAnonymously().catch(function(error) {
      // Handle Errors here.
      var errorCode = error.code;
      var errorMessage = error.message;
      // ...
    });
    firebase.auth().onAuthStateChanged(function(user) {
      if (user) {
        callback(user)
      } else {
        // User is signed out.
      }
    });
  },
  executeEvent : function(alias,payload){
    if(_events[alias])
      _events[alias](payload)
    else
      console.error('Event '+alias+' was not handled.')
  },
  on: function(alias,func){
    _events[alias] = func;
  },
  reloadOn: function(seconds = false){
    if(seconds)
      setTimeout( window.location.reload.bind( window.location ), seconds*1000 )
  }
}