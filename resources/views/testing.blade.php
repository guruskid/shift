<!DOCTYPE html>
<head>
  <title>Notify</title>
  <script src="https://js.pusher.com/7.2/pusher.min.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.css" integrity="sha512-DIW4FkYTOxjCqRt7oS9BFO+nVOwDL4bzukDyDtMO7crjUZhwpyrWBFroq+IqRe6VnJkTpRAS6nhDvf0w+wHmxg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
  <h1>Notify</h1>
  <p>
    The boy from Trenches
  </p>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js" integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
{{-- <script>

  iziToast.show({
    title: 'Hey',
    message: 'What would you like to add?'
});
</script> --}}
<script>

    // Enable pusher logging - don't include this in production
    // Pusher.logToConsole = true;

    var pusher = new Pusher('fbdb049e7d44a6f2e382', {
      cluster: 'mt1'
    });

    var channel = pusher.subscribe('notify');
    channel.bind('transaction', function(data) {
    //   alert(JSON.stringify(data));

      iziToast.success({
        timeout: 20000,
    title: 'New Transaction',
    message: data.message
});


    });
  </script>


</body>
