<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dantown Assets</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="/landingpage_assets/css/style.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
</head>

<body>

    @yield('content')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="{{asset('js/custom.js')}}"></script>
    <script>
        $("body").addClass(localStorage.getItem('dark-mode'))
        function myFunction() {
            if($('body').hasClass('dark-mode')) {
            $("body").removeClass('dark-mode')
            localStorage.removeItem('dark-mode')
            } else {
            $("body").addClass('dark-mode')
            localStorage.setItem('dark-mode', 'dark-mode');
            } 
  }
  
        
    </script>


</body>

</html>