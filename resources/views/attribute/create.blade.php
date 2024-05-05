@extends('layouts.app')
@section('title')
    Attribute
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        @php
            $links = [
            'Home'=>route('dashboard'),
            'Attribute Create'=>''
            ]
        @endphp
        <x-bread-crumb-component title='Attribute' :links="$links"/>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Horizontal Form -->
                    <form action="{{ route('attributes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Attribute Create</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-6 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" name="name" required id="name"
                                                   placeholder="Enter Name" value="{{old('name')}}">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="value" class="form-label">Value</label>
                                            <select class="form-control select2" multiple="multiple" name="values[]"
                                                    required id="value">
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary waves-effect waves-float waves-light float-right"
                                            type="submit">Submit
                                    </button>
                                </div>
                            </div>

                            <!-- /.card -->
                        </div>
                    </form>
                </div>
                <!-- /.row -->
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@push('script-bottom')


@endpush
