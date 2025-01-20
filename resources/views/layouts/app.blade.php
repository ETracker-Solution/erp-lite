<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ config('app.name', 'Cake Town') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('upload') . '/' . getSettingValue('fav_icon') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="http://localhost:8098"></script>
    @include('partials.style')
    @yield('styles')
</head>
<body class="hold-transition sidebar-mini {{$sidebar ?? ''}}">
<div class="wrapper">

    <!-- Navbar -->
    @include('partials.navbar')
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    @include('partials.sidebar')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        @yield('content')
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    @include('partials.footer')

    <style>
        .loader {
            display: none;
            border: 4px solid #f3f3f3;
            border-radius: 50%;
            border-top: 4px solid #3498db;
            width: 20px;
            height: 20px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .disabled {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>

</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
@include('partials.script')

@stack('js_scripts')
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('keypress', function (event) {
                if (event.key === 'Enter') {
                    console.log(event.key)
                    event.preventDefault(); // Prevent form submission
                }
            });
        });
    });
</script>
</body>
</html>
