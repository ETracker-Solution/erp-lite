@extends('layouts.app')
@section('title')
Store List
@endsection
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Store list'=>''
    ]
    @endphp
    <x-bread-crumb-component title='Store' :links="$links" />
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-8 col-md-8">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Store List</h3>
                        {{-- <div class="card-tools">
                            <a href="{{route('stores.create')}}"><button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp;Add Store</button></a>
                        </div> --}}
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="dataTable" class="datatables-basic table">
                            {{-- show from datatable--}}
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

            </div>
            <div class="col-lg-4 col-md-4">
                <form @if(isset($store)) action="{{route('stores.update', $store->id)}}" @else  action="{{route('stores.store')}}" @endif method="POST" class="" enctype="multipart/form-data">
                    @csrf
                    @if(isset($store))
                    @method('PUT')
                    @endif
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Store Create</h3>
                            <div class="card-tools">

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    <div class="form-group">
                                        <label for="serial_no">Store No</label>
                                        <input type="text" class="form-control" id="serial_no" name="serial_no"
                                            placeholder="" value="{{old('serial_no',isset($store) ? $store->id : $serial_no)}}" readonly>
                                        @if($errors->has('serial_no'))
                                        <small class="text-danger">{{$errors->first('serial_no')}}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    <x-forms.text label="Name" inputName="name" placeholder="Enter Name"
                                        :isRequired='true' :isReadonly='false' :defaultValue="isset($store) ? $store->name : ''" />
                                </div>
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    <x-forms.static-select label="Type" inputName="type" placeholder="Select One" :isRequired='true'  :isReadonly='false' :defaultValue="isset($store) ? $store->type : ''" :options="['fg','bp','rm','wip']"/>
                                </div>

                            </div>
                            <button class="btn btn-primary waves-effect waves-float waves-light float-right"
                                type="submit">Submit
                            </button>
                        </div>
                    </div>
                </form>
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
            ajax: '{{route('stores.index')}}',
            columns: [{
                    data: "DT_RowIndex",
                    title: "#",
                    name: "DT_RowIndex",
                    searchable: false,
                    orderable: false
                },
                {
                    data: "name",
                    title: "Name",
                    searchable: true,
                    orderable: true
                },
                {
                    data: "type",
                    title: "Type",
                    searchable: true,
                    orderable: true
                },
                {
                    data: "status",
                    title: "Status",
                    searchable: true,
                    orderable: true
                },
                {
                    data: "action",
                    title: "",
                    orderable: false,
                    searchable: false
                },
            ],
        });
    })

    // function getEditAbleData(data,(string)updateUrl){
    //    $("input[name=name]").val(data.name);
    //    $("select[name=type]").val(data.type);
    //    $('#formSubmit').attr('action', '"'+updateUrl+'"');
    //    $('#formSubmit').append('@method("PUT")');
    // }
    
</script>
@endpush
