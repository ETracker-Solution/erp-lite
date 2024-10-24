<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ config('app.name','Cake Town') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('upload').'/'.getSettingValue('fav_icon') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.partials.style')
    @yield('styles')
</head>
<body class="hold-transition sidebar-mini {{$sidebar??''}}">
<div class="wrapper">

    <!-- Navbar -->
    @include('admin.partials.navbar')
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    @include('admin.partials.sidebar')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        @yield('content')
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    @include('admin.partials.footer')
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
@include('admin.partials.script')

<script>
    function confirmAlert(element, message = "You won't be able to revert this!", buttonText = 'Yes, delete it!', title = 'Are you sure?') {
        $(document).on('click', element, function (event) {
            var form = $(this).closest("form");
            event.preventDefault();
            Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: buttonText,
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                }
            });
        });
    }
</script>
</body>
</html>
