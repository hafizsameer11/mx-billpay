<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Release Event Test</title>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.10.0/dist/echo.iife.min.js"></script>
</head>
<body>

    <h1>Account Release Event Test</h1>
    <p id="event-message">Waiting for event...</p>

    <script>
        // Set up Pusher with Laravel Echo
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '1880387', 
            cluster: 'ap1',
            forceTLS: true
        });

        const userId = '1';
        window.Echo.channel(`user.${userId}`)
            .listen('.account.released', (event) => {
                console.log("Account Released Event Received:", event.message);
                document.getElementById('event-message').innerText = event.message;
            });
    </script>

</body>
</html>
