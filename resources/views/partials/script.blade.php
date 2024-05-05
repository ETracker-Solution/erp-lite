  <!-- jQuery -->
  <script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
  <!-- Bootstrap 4 -->
  <script src="{{asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  @yield('js')
  <!-- AdminLTE App -->
  <script src="{{asset('assets/dist/js/adminlte.min.js')}}"></script>
  <script src="{{asset('assets/plugins/toastr/toastr.min.js')}}"></script>
  <script src="{{ asset('assets/plugins/chart.js/Chart.min.js')}}"></script>

{!! Toastr::message() !!}
<script>
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            toastr.error('{{$error}}', 'Error', {
                closeButton:true,
                progressBar:true,
            });
        @endforeach
    @endif
</script>
  @stack('script')
