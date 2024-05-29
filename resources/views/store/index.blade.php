@extends('layouts.app')
@section('title')
    Store List
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Store list'=>''
        ]
    @endphp
    <x-breadcrumb title='Store' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="row">

                <div class="col-lg-4 col-md-4">
                    <form @if(isset($store)) action="{{route('stores.update', $store->id)}}"
                          @else  action="{{route('stores.store')}}" @endif method="POST" class=""
                          enctype="multipart/form-data" novalidate>
                        @csrf
                        @if(isset($store))
                            @method('PUT')
                        @endif
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Store Entry</h3>
                                <div class="card-tools">

                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12 col-md-12 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="serial_no">Store No</label>
                                            <input type="text" class="form-control" id="serial_no" name="serial_no"
                                                   placeholder=""
                                                   value="{{old('serial_no',isset($store) ? $store->id : $serial_no)}}"
                                                   readonly>
                                            @if($errors->has('serial_no'))
                                                <small class="text-danger">{{$errors->first('serial_no')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12 mb-1">
                                        <x-forms.text label="Name" inputName="name" placeholder="Enter Name"
                                                      :isRequired='true' :isReadonly='false'
                                                      :defaultValue="isset($store) ? $store->name : ''"/>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12 mb-1">
                                        <x-forms.static-select label="Store Type" inputName="type"
                                                               placeholder="Select One" :isRequired='true'
                                                               :isReadonly='false'
                                                               :defaultValue="isset($store) ? $store->type : ''"
                                                               :options="['FG','BP','RM','WIP']"/>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12 mb-1">
                                        @php
                                            $store_for =[
                                                (object)[
                                                    'key'=>'ho',
                                                    'value'=>'Head Office'
                                                ],
                                                (object)[
                                                    'key'=>'factory',
                                                    'value'=>'Factory'
                                                ],
                                                (object)[
                                                    'key'=>'outlet',
                                                    'value'=>'Outlet'
                                                ],
                                            ];
                                        @endphp
                                        <x-forms.select label="Store Of" inputName="doc_type" placeholder="Select One"
                                                        :isRequired='true' :isReadonly='false'
                                                        :defaultValue="isset($store) ? $store->doc_type : ''"
                                                        :options="$store_for" optionId="key" optionValue="value"/>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12 mb-1" id="outletDropdown" hidden>
                                        <x-forms.select label="Outlet" inputName="outlet_id" placeholder="Select One"
                                                        :isRequired='true' :isReadonly='false'
                                                        :defaultValue="isset($store) && $store->doc_type == 'outlet' ? $store->doc_id : ''"
                                                        :options="$outlets" optionId="id" optionValue="name"/>
                                    </div>
                                    <div class="col-xl-12 col-md-12 col-12 mb-1" id="factoryDropdown" hidden>
                                        <x-forms.select label="Factory" inputName="factory_id" placeholder="Select One"
                                                        :isRequired='true' :isReadonly='false'
                                                        :defaultValue="isset($store) && $store->doc_type == 'factory'  ? $store->doc_id : ''"
                                                        :options="$factories" optionId="id" optionValue="name"/>
                                    </div>

                                </div>
                                <button class="btn btn-info waves-effect waves-float waves-light float-right ml-1"
                                        type="submit">Submit
                                </button>
                                <a href="{{ route('stores.index') }}"
                                   class="btn btn-warning waves-effect waves-float waves-light float-right">Refresh</a>
                            </div>
                        </div>
                    </form>
                </div>
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
        $(document).ready(function () {
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

        var docType = $('select[name=doc_type]').val();
        if (docType === 'factory') {
            $('#factoryDropdown').prop('hidden', false)
            $('#outletDropdown').prop('hidden', true)
        } else if (docType === 'outlet') {
            $('#factoryDropdown').prop('hidden', true)
            $('#outletDropdown').prop('hidden', false)
        } else {
            $('#factoryDropdown').prop('hidden', true)
            $('#outletDropdown').prop('hidden', true)
        }

        $('select[name=doc_type]').on('select2:select', function (e) {
            const docType = e.params.data.id;
            $('select[name=outlet_id]').val('')
            $('select[name=factory_id]').val('')
            if (docType === 'factory') {
                $('#factoryDropdown').prop('hidden', false)
                $('#outletDropdown').prop('hidden', true)
            } else if (docType === 'outlet') {
                $('#factoryDropdown').prop('hidden', true)
                $('#outletDropdown').prop('hidden', false)
            } else {
                $('#factoryDropdown').prop('hidden', true)
                $('#outletDropdown').prop('hidden', true)
            }
        })
        $('select[name=outlet_id]').on('select2:select', function (e) {
            const docid = e.params.data.id;
            $('select[name=outlet_id]').attr('name', 'doc_id')
        })
        $('select[name=factory_id]').on('select2:select', function (e) {
            const docid = e.params.data.id;
            $('select[name=factory_id]').attr('name', 'doc_id')
        })

    </script>
@endpush
