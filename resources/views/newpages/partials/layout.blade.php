<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
  <title>Dashboard - Dantown Multi Services </title>
  <link rel="icon" type="image/x-icon" href="assets/img/fav2.png" />
  <link href="/newpages/assets/css/loader.css" rel="stylesheet" type="text/css" />
  <script src="/newpages/assets/js/loader.js"></script>
  <!-- BEGIN GLOBAL MANDATORY STYLES -->
  <link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap" rel="stylesheet">
  <link href="/newpages/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="/newpages/assets/css/plugins.css" rel="stylesheet" type="text/css" />
  <!-- END GLOBAL MANDATORY STYLES -->

  <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
  <link href="/newpages/plugins/apex/apexcharts.css" rel="stylesheet" type="text/css">
  <link href="/newpages/assets/css/dashboard/dash_1.css" rel="stylesheet" type="text/css" />
  <link href="/newpages/css/main.css" rel="stylesheet" type="text/css" />
  <link href=" {{asset('user_main.css')}} " rel="stylesheet">
  <link href=" {{asset('user_assets/css/responsive-fixes.css')}} " rel="stylesheet">
  <link href=" {{asset('css/app.css')}} " rel="stylesheet">
  <link href=" {{asset('user_main.css')}} " rel="stylesheet">
  <link href=" {{asset('custom.css?v = 1.0')}} " rel="stylesheet">
  <link href="{{asset('user_assets/OwlCarousel/assets/owl.carousel.css')}} " rel="stylesheet">
  <link href="{{asset('user_assets/OwlCarousel/assets/owl.theme.default.min.css')}} " rel="stylesheet">
  <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

</head>

<body class="sidebar-noneoverflow">
  @yield('content')



  <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
  <script src="/newpages/assets/js/libs/jquery-3.1.1.min.js"></script>
  <script src="/newpages/bootstrap/js/popper.min.js"></script>
  <script src="/newpages/bootstrap/js/bootstrap.min.js"></script>
  <script src="/newpages/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
  <script src="https://unpkg.com/ionicons@5.1.2/dist/ionicons.js"></script>


  <script src="/js/app.js?v = 1.4"></script>
  <script src="{{asset('assets/scripts/main.js')}}"></script>
  <script src="{{asset('js/custom.js')}}"></script>

  <script src="/newpages/assets/js/app.js"></script>
  <script src="/newpages/js/main.js"></script>
  <script>
    $(document).ready(function() {
        App.init();
    })
                            
  </script>
  <script src="/newpages/assets/js/custom.js"></script>
  <!-- END GLOBAL MANDATORY SCRIPTS -->

  <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
  <script src="/newpages/plugins/apex/apexcharts.min.js"></script>
  <script src="/newpages/assets/js/dashboard/dash_1.js"></script>

</body>

</html>