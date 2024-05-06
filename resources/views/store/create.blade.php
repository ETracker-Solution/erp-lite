@extends('layouts.app')
@section('title')
    Store
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        @php
            $links = [
            'Home'=>route('dashboard'),
            'Store Create'=>''
            ]
        @endphp
        <x-bread-crumb-component title='Store' :links="$links"/>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Horizontal Form -->
                    <form action="{{route('stores.store')}}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Store Create</h4>
                            </div>
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
                                            <label for="type" class="control-label">Type</label>
                                            <select class="form-control" name="type">
                                                <option value=" ">Select One</option>
                                                <option value="fg">FG</option>
                                                <option value="bp">BP</option>
                                                <option value="rm">RM</option>
                                                <option value="wip">WIP</option>
                                            </select>
                                            @if ($errors->has('type'))
                                                <small
                                                    class="text-danger">{{ $errors->first('type') }}</small>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                                <button class="btn btn-primary waves-effect waves-float waves-light float-right"
                                        type="submit">Submit
                                </button>
                            </div>
                        </div>
                </div>
                </form>
                <!-- /.card -->
            </div>
            <div class="col-2"></div>
        </div>
        <!-- /.row -->

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@push('script-bottom')


@endpush
