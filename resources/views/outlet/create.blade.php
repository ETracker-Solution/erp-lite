@extends('layouts.app')
@section('title')
    Outlet
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
            'Outlet' . (isset($outlet) ? ' Edit' : ' Entry') => '',
        ];
    @endphp
    <x-breadcrumb title='Outlet' :links="$links" />

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form
                        @if (isset($outlet)) action="{{ route('outlets.update', $outlet->id) }}" @else action="{{ route('outlets.store') }}" @endif
                        method="POST" class="" enctype="multipart/form-data">
                        @csrf
                        @if (isset($outlet))
                            @method('PUT')
                        @endif
                        <!-- Horizontal Form -->
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Outlet</h3>
                                <div class="card-tools">
                                    <a href="{{ route('outlets.index') }}" class="btn btn-sm btn-primary">
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
                                            <label for="serial_no">Outlet No</label>
                                            <input type="text" class="form-control" id="serial_no" name="serial_no"
                                                placeholder=""
                                                value="{{ old('serial_no', isset($outlet) ? $outlet->id : $serial_no) }}"
                                                readonly>
                                            @if ($errors->has('serial_no'))
                                                <small class="text-danger">{{ $errors->first('serial_no') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <x-forms.text label="Name" inputName="name" placeholder="Enter Name"
                                            :isRequired='true' :isReadonly='false' :defaultValue="isset($outlet) ? $outlet->name : ''" />
                                    </div>

                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <x-forms.text label="Address" inputName="address" placeholder="Enter Address"
                                            :isRequired='true' :isReadonly='false' :defaultValue="isset($outlet) ? $outlet->address : ''" />
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button class="btn btn-info float-right"><i class="fa fa-check" aria-hidden="true"></i>
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

@endpush
