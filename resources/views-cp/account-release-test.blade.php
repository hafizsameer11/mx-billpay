<!DOCTYPE html>
<head>
    <title>Pusher Test</title>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Enable Pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('0ad9e7f7e71dc1522ad6', {
            cluster: 'ap1'
        });

        var userId = 1;
        var channel = pusher.subscribe(`user.${userId}`);

        channel.bind('account.released', function(data) {
            alert("Event received: " + data.message);
        });
    </script>
</head>
<body>
    <h1>Pusher Test</h1>
    <p>Waiting for the Account Released event...</p>
</body>
