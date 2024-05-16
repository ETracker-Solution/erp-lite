@extends('layouts.app')
@section('title')
Batch List
@endsection
@section('content')
<!-- Content Header (Page header) -->
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Batch list'=>''
    ]
    @endphp
    <x-breadcrumb title='Batch' :links="$links"/>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <form @if(isset($batch)) action="{{route('batches.update', $batch->id)}}" @else action="{{route('batches.store')}}" @endif method="POST" class="" enctype="multipart/form-data">
                            @csrf
                            @if(isset($batch))
                            @method('PUT')
                            @endif
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Batch Create</h3>
                                    <div class="card-tools">

                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-12 mb-1">
                                            <div class="form-group">
                                                <label for="serial_no">Batch ID</label>
                                                <input type="text" class="form-control" id="serial_no" name="serial_no"
                                            placeholder="" value="{{old('serial_no',isset($batch) ? $batch->id : $serial_no)}}" readonly>
                                                @if($errors->has('serial_no'))
                                                <small class="text-danger">{{$errors->first('serial_no')}}</small>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-xl-12 col-md-12 col-12 mb-1">
                                            <x-forms.text label="Batch No" inputName="batch_no"
                                                placeholder="Enter Batch No" :isRequired='true' :isReadonly='false'
                                                :defaultValue="isset($batch) ? $batch->batch_no : ''" />
                                        </div>
                                        <div class="col-xl-12 col-md-12 col-12 mb-1">
                                            <div class="form-group">
                                                <label for="serial_no">Batch Date</label>
                                                <div class="input-group date" id="reservationdate"
                                                    data-target-input="nearest">
                                                    <input type="text" name="date" value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
                                                        data-target="#reservationdate" />
                                                    <div class="input-group-append" data-target="#reservationdate"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-xl-12 col-md-12 col-12 mb-1">
                                            <x-forms.text label="Production Manager" inputName="p_manager"
                                                placeholder="Enter Production Manager" :isRequired='true'
                                                :isReadonly='false' :defaultValue="isset($batch) ? $batch->p_manager : ''" />
                                        </div>
                                        <div class="col-xl-12 col-md-12 col-12 mb-1">
                                            <div class="form-group">
                                                <label for="serial_no">Batch Description</label>
                                                <textarea class="form-control" name="description" id="" cols="10"
                                                    rows="3">{{ old('description',isset($batch) ? $batch->description : '') }}</textarea>
                                            </div>
                                        </div>

                                    </div>
                                    <button class="btn btn-info waves-effect waves-float waves-light float-right"
                                        type="submit">Submit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-8 col-md-8">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Batch List</h3>
                                {{-- <div class="card-tools">
                                    <a href="{{route('stores.create')}}"><button class="btn btn-sm btn-primary"><i
                                                class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp;Add
                                            Store</button></a>
                                </div> --}}
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="dataTable" class="table table-bordered table-hover">
                                    {{-- show from datatable--}}
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->

    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

@endsection
@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
@endsection
@section('js')
<!-- DataTables -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
@endsection
@push('script')
<script>
    $(document).ready(function() {
        $('#dataTable').dataTable({
            stateSave: true,
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: '{{route('batches.index')}}',
            columns: [{
                    data: "DT_RowIndex",
                    title: "#",
                    name: "DT_RowIndex",
                    searchable: false,
                    orderable: false
                },
                {
                    data: "batch_no",
                    title: "Batch",
                    searchable: true,
                    orderable: true
                },
                {
                    data: "date",
                    title: "Date",
                    searchable: true,
                    orderable: true
                },
                {
                    data: "p_manager",
                    title: "Production Manager",
                    searchable: true,
                    orderable: true
                },
                {
                    data: "description",
                    title: "Description",
                    searchable: true,
                    orderable: true
                },
                {
                    data: "action",
                    title: "Action",
                    orderable: false,
                    searchable: false
                },
            ],
        });
    })

    function getEditAbleData(data){
        // console.log(data.type).val();
        data.name = $('input[name="name"]').val();
    }
    
</script>
@endpush