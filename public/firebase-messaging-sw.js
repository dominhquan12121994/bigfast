importScripts('./js/firebase.js');
importScripts('./js/firebase-messaging.js');

firebase.initializeApp({
    apiKey: "AIzaSyDVQtJj9xIAD_DlwA2Zp38JF9Hk3AECkcU",
    authDomain: "bigfast-aab62.firebaseapp.com",
    projectId: "bigfast-aab62",
    storageBucket: "bigfast-aab62.appspot.com",
    messagingSenderId: "977559176587",
    appId: "1:977559176587:web:ce0f65e274b53f2dc75b8e",
    measurementId: "G-LFSZ1NNFP5"
});

const messaging = firebase.messaging();

var countMessage = 0;
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    countMessage++;
    const notificationTitle = "BigFast";
    const notificationOptions = {
        body: payload.data.title,
    };

    self.registration.showNotification(notificationTitle,
        notificationOptions);
});
