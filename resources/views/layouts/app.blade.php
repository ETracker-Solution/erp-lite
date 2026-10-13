<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ getSettingValue('company_name', config('app.name','Cake Town')) }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('upload').'/'.getSettingValue('fav_icon') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.style')
    @yield('styles')
</head>
<body class="hold-transition sidebar-mini {{$sidebar??''}}">
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
        document.querySelectorAll('form.prevent-enter-submit').forEach(function (form) {
            form.addEventListener('keypress', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
        });
    });
</script>
<script>
    $(document).on('click', '#logout-button', function () {
        sessionStorage.clear();
    });

    $(document).on('click', '#btn-sidebar-backup', function (e) {
        e.preventDefault();
        var backupUrl = "{{ route('database.backup') }}";
        var generateUrl = "{{ route('database.backup.generate') }}";
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: 'Database Backup',
            text: 'Download the database backup ZIP file instantly now.',
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: '<i class="fas fa-download mr-1"></i> Instant Download',
            denyButtonText: '<i class="fas fa-sync-alt mr-1"></i> Generate Fresh & Download',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#28a745',
            denyButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            customClass: {
                confirmButton: 'btn btn-success m-1',
                denyButton: 'btn btn-info m-1',
                cancelButton: 'btn btn-secondary m-1'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.isConfirmed) {
                // Instant download
                window.location.href = backupUrl;
                Swal.fire({
                    title: 'Downloading...',
                    text: 'Your backup ZIP file download has started.',
                    icon: 'success',
                    timer: 2500,
                    showConfirmButton: false
                });
            } else if (result.isDenied) {
                // Generate fresh backup then download
                Swal.fire({
                    title: 'Generating Fresh Backup...',
                    text: 'Dumping and compressing database. Please wait...',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: function () {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: generateUrl,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    success: function (res) {
                        window.location.href = backupUrl;
                        Swal.fire({
                            title: 'Backup Ready!',
                            text: 'Fresh backup generated. Download has started.',
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    },
                    error: function () {
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to generate backup. Downloading latest backup instead.',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        }).then(function () {
                            window.location.href = backupUrl;
                        });
                    }
                });
            }
        });
    });
</script>
</body>
</html>
