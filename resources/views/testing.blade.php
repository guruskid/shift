<!DOCTYPE html>
<head>
  <title>Notify</title>
  <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
  <script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('fbdb049e7d44a6f2e382', {
      cluster: 'mt1'
    });

    var channel = pusher.subscribe('complaints');
    channel.bind('my-event', function(data) {
      alert(JSON.stringify(data));
    });
  </script>
</head>
<body>
  <h1>Notify</h1>
  <p>
    The boy from Trenches
  </p>
</body>
