@extends('layouts.app')
@section('title')
    Unit
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        @php
            $links = [
            'Home'=>route('dashboard'),
            'Unit Create'=>''
            ]
        @endphp
        <x-bread-crumb-component title='Unit' :links="$links"/>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Horizontal Form -->
                    <form action="{{route('units.store')}}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Unit Create</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="unit_no">Unit No</label>
                                            <input type="text" class="form-control" id="unit_no" name="unit_no"
                                                   placeholder="" value="{{old('unit_no',$unit_no)}}" readonly>
                                            @if($errors->has('unit_no'))
                                                <small class="text-danger">{{$errors->first('unit_no')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                   placeholder="Ex: Kilogram, Piece, Liter, Box" value="{{old('name')}}">
                                            @if($errors->has('name'))
                                                <small class="text-danger">{{$errors->first('name')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="short_name">Short Name</label>
                                            <input type="text" class="form-control" id="short_name" name="short_name"
                                                   placeholder="Ex: Kg, Pcs, L, Box" value="{{old('short_name')}}">
                                            @if($errors->has('short_name'))
                                                <small class="text-danger">{{$errors->first('short_name')}}</small>
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
