@extends('layouts.app')
@section('title')
    Supllier Group
@endsection
@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Supllier Group' . (isset($supplierGroup) ? ' Edit' : ' Create') => '',
        ];
    @endphp
    <x-breadcrumb title='Supllier Group' :links="$links" />

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form
                        @if (isset($supplierGroup)) action="{{ route('supplier-groups.update', $supplierGroup->id) }}" @else action="{{ route('supplier-groups.store') }}" @endif
                        method="POST" class="" enctype="multipart/form-data">
                        @csrf
                        @if (isset($supplierGroup))
                            @method('PUT')
                        @endif
                        <!-- Horizontal Form -->
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Supplier Group</h3>
                                <div class="card-tools">
                                    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list" aria-hidden="true"></i>
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
                                            <label for="serial_no">Supplier Group No</label>
                                            <input type="text" class="form-control" id="serial_no" name="serial_no"
                                                placeholder=""
                                                value="{{ old('serial_no', isset($supplierGroup) ? $supplierGroup->id : $serial_no) }}"
                                                readonly>
                                            @if ($errors->has('serial_no'))
                                                <small class="text-danger">{{ $errors->first('serial_no') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <x-forms.text label="Name" inputName="name" placeholder="Enter Name"
                                            :isRequired='true' :isReadonly='false' :defaultValue="isset($supplierGroup) ? $supplierGroup->name : ''" />
                                    </div>

                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <x-forms.text label="Code" inputName="code" placeholder="Enter Code"
                                            :isRequired='true' :isReadonly='false' :defaultValue="isset($supplierGroup) ? $supplierGroup->code : ''" />
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button class="btn btn-primary float-right"><i class="fa fa-check" aria-hidden="true"></i>
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
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
@endpush
