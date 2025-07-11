// Scripts for firebase and firebase messaging
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in the messagingSenderId.
// Ganti dengan konfigurasi Firebase Anda
const firebaseConfig = {
    apiKey: "AIzaSyAZtIr-kxrsihT63SM6JzuQhB9Es4dN-CE",
    authDomain: "voucherwave-18624.firebaseapp.com",
    projectId: "voucherwave-18624",
    storageBucket: "voucherwave-18624.firebasestorage.app",
    messagingSenderId: "504900538895",
    appId: "1:504900538895:web:9a7f95540af909c853497a"
};

firebase.initializeApp(firebaseConfig);

// Retrieve an instance of Firebase Messaging so that it can handle background messages.
const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  console.log('Received background message ', payload);

  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: asset('images/VoucherWaveLogo.png')
  };

  return self.registration.showNotification(notificationTitle, notificationOptions);
});