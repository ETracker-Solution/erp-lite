@extends('admin.layouts.app')

@section('title', 'Business Edit')
@section('content')
<div class="content-wrapper">
    @php
        $links = [
            'Home'=>route('admin.dashboard'),
            'Business list'=>route('admin.businesses.index'),
            'Business Edit'=>'',
        ]
    @endphp
    <x-bread-crumb-component title='Business' :links="$links" />
    <div class="content-body">
        <!-- Basic Inputs start -->
        <section id="basic-input">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Business Edit</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{route('admin.businesses.update',$business->id)}}" method="POST" class="" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" value="{{$business->name}}" name="name" placeholder="Enter Name">
                                            @if($errors->has('name'))
                                                <small class="text-danger">{{$errors->first('name')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <input type="address" class="form-control" id="address" value="{{$business->address}}" name="address" placeholder="Enter address">

                                            @if($errors->has('address'))
                                                <small class="text-danger">{{$errors->first('address')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                   
                                </div>
                                <button class="btn btn-primary waves-effect waves-float waves-light" type="submit">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Basic Inputs end -->
    </div>
</div>

@endsection
@section('css')

@endsection
@section('js')

@endsection
@push('script')

@endpush
