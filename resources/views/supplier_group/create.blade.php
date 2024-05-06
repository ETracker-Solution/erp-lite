@extends('layouts.app')
@section('title')
    Supllier Group
@endsection
@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-1">

            </div>
        </div><!-- /.container-fluid -->
    </section>



    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="{{route('supplier-groups.store')}}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        <!-- Horizontal Form -->
                        <div class="card card-primary">
                            <div class="card-header bg-light">
                                <h3 class="card-title" style="color:#115548;">Supplier Group</h3>
                                <div class="card-tools">
                                    <a href="{{route('suppliers.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list"
                                           aria-hidden="true"></i>
                                        &nbsp;See List

                                    </a>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->


                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="serial_no">Store No</label>
                                            <input type="text" class="form-control" id="serial_no" name="serial_no"
                                                   placeholder="" value="{{old('serial_no',$serial_no)}}" readonly>
                                            @if($errors->has('serial_no'))
                                                <small class="text-danger">{{$errors->first('serial_no')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                   placeholder="Enter Name"
                                                   value="{{old('name')}}">
                                            @if($errors->has('name'))
                                                <small class="text-danger">{{$errors->first('name')}}</small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="code">Code</label>
                                            <input type="text" class="form-control" id="code" name="code"
                                                   placeholder="Enter Code"
                                                   value="{{old('code')}}">
                                            @if($errors->has('code'))
                                                <small class="text-danger">{{$errors->first('code')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button class="btn btn-primary float-right"><i class="fa fa-check"
                                                                          aria-hidden="true"></i>
                                    Submit
                                </button>
                            </div>
                        </div>
                        <!-- /.card -->
                    </form>
                </div>
                <div class="col-2"></div>
            </div>
            <!-- /.row -->

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@push('js')
    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            //Datemask dd/mm/yyyy
            $('#datemask').inputmask('dd/mm/yyyy', {'placeholder': 'dd/mm/yyyy'})
            //Datemask2 mm/dd/yyyy
            $('#datemask2').inputmask('mm/dd/yyyy', {'placeholder': 'mm/dd/yyyy'})
            //Money Euro
            $('[data-mask]').inputmask()

            //Date range picker
            $('#reservation').daterangepicker()
            //Date range picker with time picker
            $('#reservationtime').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                locale: {
                    format: 'MM/DD/YYYY hh:mm A'
                }
            })
            //Date range as a button
            $('#daterange-btn').daterangepicker(
                {
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function (start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
                }
            )

            //Timepicker
            $('#timepicker').datetimepicker({
                format: 'LT'
            })

            //Bootstrap Duallistbox
            $('.duallistbox').bootstrapDualListbox()

            //Colorpicker
            $('.my-colorpicker1').colorpicker()
            //color picker with addon
            $('.my-colorpicker2').colorpicker()

            $('.my-colorpicker2').on('colorpickerChange', function (event) {
                $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
            });

            $("input[data-bootstrap-switch]").each(function () {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });

        })
    </script>

@endpush
