@extends('layouts.app')
@section('title')
Unit List
@endsection
@section('content')
<!-- Content Header (Page header) -->
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Data Admin Module'=>'',
    'Inventory Setting'=>'',
    'Unit list'=>''
    ]
    @endphp
    <x-breadcrumb title='Unit' :links="$links"/>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-4">
                <form @if(isset($unit)) action="{{route('units.update', $unit->id)}}" @else action="{{route('units.store')}}" @endif method="POST" class="" enctype="multipart/form-data">
                    @csrf
                    @if(isset($unit))
                        @method('PUT')
                        @endif
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Unit Entry</h3>
                            <div class="card-tools">

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    {{--
                                    <x-forms.text label="Unit No" inputName="unit_no" placeholder="Enter Unit No"
                                        :isRequired='true' value="{{old('unit_no',$unit_no)}}" :isReadonly='true'
                                        defaultValue="" /> --}}
                                    <div class="form-group">
                                        <label for="unit_no">Unit No</label>
                                        <input type="text" class="form-control" id="unit_no" name="unit_no"
                                            placeholder="" value="{{old('unit_no',isset($unit) ? $unit->id : $unit_no)}}" readonly>
                                        @if($errors->has('unit_no'))
                                        <small class="text-danger">{{$errors->first('unit_no')}}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    <x-forms.text label="Name" inputName="name" placeholder="Enter Name"
                                        :isRequired='true' :isReadonly='false' :defaultValue="isset($unit) ? $unit->name : ''" />
                                </div>
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    <x-forms.text label="Short Name" inputName="short_name"
                                        placeholder="Enter Short Name" :isRequired='true' :isReadonly='false'
                                        :defaultValue="isset($unit) ? $unit->short_name : ''" />
                                </div>
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    <x-forms.static-select label="Status" inputName="status" placeholder="Select One" :isRequired='true'  :isReadonly='false' :defaultValue="isset($unit) ? $unit->status : ''" :options="['active','inactive']"/>
                                </div>

                            </div>
                            <button class="btn btn-info waves-effect waves-float waves-light float-right ml-1"
                                type="submit">Submit
                            </button>
                            <a href="{{ route('units.index') }}"
                                   class="btn btn-warning waves-effect waves-float waves-light float-right">Refresh</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-8 col-md-8">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Unit List</h3>
                        {{-- <div class="card-tools">
                            <a href="{{route('units.create')}}"><button class="btn btn-sm btn-primary"><i
                                        class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp;Add Unit</button></a>
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
            ajax: '{{route('units.index')}}',
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
                    data: "status",
                    title: "Status",
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
</script>
@endpush