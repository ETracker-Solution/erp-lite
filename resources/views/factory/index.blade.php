@extends('layouts.app')
@section('title')
Factory List
@endsection
@section('content')
<!-- Content Header (Page header) -->
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Data Admin Module'=>'',
    'Factory list'=>''
    ]
    @endphp
    <x-breadcrumb title='Factory' :links="$links"/>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-4">
                <form @if(isset($factory)) action="{{route('factories.update', $factory->id)}}" @else action="{{route('factories.store')}}" @endif method="POST" class="" enctype="multipart/form-data">
                    @csrf
                    @if(isset($factory))
                        @method('PUT')
                        @endif
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Factory Entry</h3>
                            <div class="card-tools">

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    <div class="form-group">
                                        <label for="uid">Factory No</label>
                                        <input type="text" class="form-control" id="uid" name="uid"
                                            placeholder="" value="{{old('uid',isset($factory) ? $factory->id : $uid)}}" readonly>
                                        @if($errors->has('uid'))
                                        <small class="text-danger">{{$errors->first('uid')}}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    <x-forms.text label="Name" inputName="name" placeholder="Enter Name"
                                        :isRequired='true' :isReadonly='false' :defaultValue="isset($factory) ? $factory->name : ''" />
                                </div>
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    <x-forms.text label="Addres" inputName="address"
                                        placeholder="Enter Short Name" :isRequired='true' :isReadonly='false'
                                        :defaultValue="isset($factory) ? $factory->address : ''" />
                                </div>
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    <x-forms.static-select label="Status" inputName="status" placeholder="Select One" :isRequired='true'  :isReadonly='false' :defaultValue="isset($factory) ? $factory->status : ''" :options="['active','inactive']"/>
                                </div>

                            </div>
                            <button class="btn btn-info waves-effect waves-float waves-light float-right ml-1"
                                type="submit">Submit
                            </button>
                            <a href="{{ route('factories.index') }}"
                                   class="btn btn-warning waves-effect waves-float waves-light float-right">Refresh</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-8 col-md-8">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Factory List</h3>
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
            ajax: '{{route('factories.index')}}',
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
                    data: "address",
                    title: "Address",
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