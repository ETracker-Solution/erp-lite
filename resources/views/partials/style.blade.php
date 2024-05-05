  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{asset('assets/plugins/fontawesome-free/css/all.min.css')}}">
  @yield('css')
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('assets/dist/css/adminlte.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/plugins/toastr/toastr.min.css')}}">
  <style>
      .active-color {
          background-color: #6a8871 !important;
          color: #fff !important;
          background: linear-gradient(0deg, rgba(34, 193, 195, 1) 0%, #3d9970 100%)
      }

      .active-color:hover {
          background-color: #6a8871 !important;
          color: #fff !important;
      }

      aside a:hover {
          color: #fff !important;
          background-color: #6a8871 !important;
      }
  </style>

  @stack('style')
