@extends('layouts.app')

@section('title', 'Member Type Edit')
@section('content')
@php
$links = [
    'Home'=>route('dashboard'),
    'Member Type list'=>route('member-types.index'),
    'Member Type Edit'=>'',
]
@endphp
<x-breadcrumb title='Member Type' :links="$links" />
<section class="content">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-info">
                <div class="card-header">
                    <h4 class="card-title">Member Type Edit</h4>
                </div>
                <div class="card-body">
                    <form action="{{route('member-types.update',encrypt($memberType->id))}}" method="POST" class="" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="col-xl-4 col-md-8 col-12 mb-1">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" value="{{$memberType->name}}" name="name" placeholder="Enter Name">
                                    @if($errors->has('name'))
                                        <small class="text-danger">{{$errors->first('name')}}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-8 col-12 mb-1">
                                <div class="form-group">
                                    <label for="from_point">From Point</label>
                                    <input type="number" class="form-control" id="from_point"
                                           value="{{$memberType->from_point}}" name="from_point"
                                           placeholder="Enter From Point">

                                    @if($errors->has('from_point'))
                                        <small class="text-danger">{{$errors->first('from_point')}}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-8 col-12 mb-1">
                                <div class="form-group">
                                    <label for="to_point">To Point</label>
                                    <input type="number" class="form-control" id="to_point"
                                           value="{{$memberType->to_point}}" name="to_point"
                                           placeholder="Enter From Point">

                                    @if($errors->has('to_point'))
                                        <small class="text-danger">{{$errors->first('to_point')}}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-8 col-12 mb-1">
                                <div class="form-group">
                                    <label for="minimum_purchase">Minimum Purchase</label>
                                    <input type="number" class="form-control" id="minimum_purchase"
                                           value="{{$memberType->minimum_purchase}}" name="minimum_purchase"
                                           placeholder="Enter From Point">

                                    @if($errors->has('minimum_purchase'))
                                        <small class="text-danger">{{$errors->first('minimum_purchase')}}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-8 col-12 mb-1">
                                <div class="form-group">
                                    <label for="discount">Discount</label>
                                    <input type="number" class="form-control" id="discount"
                                           value="{{$memberType->discount}}" name="discount"
                                           placeholder="Enter From Point">

                                    @if($errors->has('discount'))
                                        <small class="text-danger">{{$errors->first('discount')}}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-info waves-effect waves-float waves-light float-right" type="submit">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Basic Inputs end -->
</section>

@endsection
@section('css')

@endsection
@section('js')

@endsection
@push('script')

@endpush
