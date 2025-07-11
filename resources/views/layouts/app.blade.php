<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Midtrans Snap.js -->
    <script type="text/javascript"
      src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
      data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    
    <!-- Swiper JS for Banners -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
    
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    
    @auth
    <script>
        // Konfigurasi Firebase diambil dari file config/services.php yang membaca .env
        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}"
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        function requestNotificationPermission() {
            console.log('Requesting permission...');
            Notification.requestPermission().then((permission) => {
                if (permission === 'granted') {
                    console.log('Notification permission granted.');
                    
                    // Get token
                    messaging.getToken({ vapidKey: "{{ config('services.firebase.vapid_key') }}" }).then((currentToken) => {
                        if (currentToken) {
                            console.log('FCM Token:', currentToken);
                            saveTokenToServer(currentToken);
                        } else {
                            console.log('No registration token available. Request permission to generate one.');
                        }
                    }).catch((err) => {
                        console.log('An error occurred while retrieving token. ', err);
                    });
                } else {
                    console.log('Unable to get permission to notify.');
                }
            });
        }

        function saveTokenToServer(token) {
            fetch('/fcm-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ token: token })
            }).then(response => response.json())
              .then(data => console.log(data.message))
              .catch(error => console.error('Error saving token:', error));
        }

        // Coba minta izin saat halaman dimuat, tapi jangan memaksa.
        // Browser modern mungkin memblokir ini jika tidak dipicu oleh aksi pengguna.
        if (Notification.permission === 'default') {
             console.log('Notification permission is default. Waiting for user action.');
        } else if(Notification.permission === 'granted') {
             requestNotificationPermission();
        }

        // Event listener untuk tombol manual
        document.addEventListener('DOMContentLoaded', () => {
            const enableNotifButton = document.getElementById('enable-notifications-button');
            if(enableNotifButton) {
                enableNotifButton.addEventListener('click', requestNotificationPermission);
            }
        });

        messaging.onMessage((payload) => {
            console.log('Message received. ', payload);
            // Tampilkan notifikasi di foreground
            const notification = new Notification(payload.notification.title, {
                body: payload.notification.body,
                icon: payload.notification.icon
            });
        });
    </script>
    @endauth
    @stack('scripts')
</body>
</html>
