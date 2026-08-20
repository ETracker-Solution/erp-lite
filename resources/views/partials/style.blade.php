  <!-- Google Font: Source Sans Pro -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{asset('assets/plugins/fontawesome-free/css/all.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">

  <link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

  <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">

  <link rel="stylesheet" href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">

  <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

  <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}">
  @yield('css')
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('assets/dist/css/adminlte.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/plugins/toastr/toastr.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/css/erp-ui.css') }}">

  



 


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
