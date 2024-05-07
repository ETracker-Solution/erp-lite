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
                                    {{-- <x-forms.text label="Unit No" inputName="unit_no" placeholder="Enter Unit No" :isRequired='true' value="{{old('unit_no',$unit_no)}}" :isReadonly='true' defaultValue=""/> --}}



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
                                    <x-forms.text label="Name" inputName="name" placeholder="Enter Name" :isRequired='true'  :isReadonly='false' defaultValue=""/>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                    <x-forms.text label="Short Name" inputName="short_name" placeholder="Enter Short Name" :isRequired='true'  :isReadonly='false' defaultValue=""/>
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
