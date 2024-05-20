@extends('layouts.app')
@section('title', 'Member Point Settings')
@section('content')
@php
$links = [
'Home'=>route('dashboard'),
'Member Point Settings'=>route('member-points.index'),
'Member Point Settings create'=>''
]
@endphp
<x-breadcrumb title='Member Point Settings create' :links="$links" />
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h4 class="card-title">Member Point Settings Create</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{route('member-points.store')}}" method="POST" class=""
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-xl-4 col-md-8 col-12 mb-1">
                                    <div class="form-group">
                                        <label for="member_type_id">Member Type</label>
                                        <select class="select2 form-control" name="member_type_id">
                                            <option value="" selected disabled>Select One</option>
                                            @forelse($memberTypes as $row)
                                            <option value="{{ $row->id }}" {{ old('member_type_id')==$row->id ?
                                                'selected' : '' }}>{{ $row->name }}
                                            </option>
                                            @empty
                                            @endforelse
                                        </select>
                                        @if($errors->has('member_type_id'))
                                        <small class="text-danger">{{$errors->first('member_type_id')}}</small>
                                        @endif
                                    </div>
                                </div>
                                {{-- <div class="col-xl-4 col-md-8 col-12 mb-1">--}}
                                    {{-- <div class="form-group">--}}
                                        {{-- <label for="from_amount">From Amount</label>--}}
                                        {{-- <input type="text" class="form-control" id="from_amount" --}} {{--
                                            name="from_amount" placeholder="Enter From Amount" --}} {{--
                                            value="{{old('from_amount')}}">--}}
                                        {{-- @if($errors->has('from_amount'))--}}
                                        {{-- <small class="text-danger">{{$errors->first('from_amount')}}</small>--}}
                                        {{-- @endif--}}
                                        {{-- </div>--}}
                                    {{-- </div>--}}
                                {{-- <div class="col-xl-4 col-md-8 col-12 mb-1">--}}
                                    {{-- <div class="form-group">--}}
                                        {{-- <label for="to_amount">To Amount</label>--}}
                                        {{-- <input type="text" class="form-control" id="to_amount" name="to_amount"
                                            --}} {{-- placeholder="Enter To Amount" value="{{old('to_amount')}}">--}}
                                        {{-- @if($errors->has('to_amount'))--}}
                                        {{-- <small class="text-danger">{{$errors->first('to_amount')}}</small>--}}
                                        {{-- @endif--}}
                                        {{-- </div>--}}
                                    {{-- </div>--}}
                                <div class="col-xl-4 col-md-8 col-12 mb-1">
                                    <div class="form-group">
                                        <label for="per_amount">Per Amount</label>
                                        <input type="text" class="form-control" id="per_amount" name="per_amount"
                                            placeholder="Enter To Amount" value="{{old('per_amount')}}">
                                        @if($errors->has('per_amount'))
                                        <small class="text-danger">{{$errors->first('per_amount')}}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-8 col-12 mb-1">
                                    <div class="form-group">
                                        <label for="point">Point</label>
                                        <input type="number" class="form-control" id="point" name="point"
                                            placeholder="Enter Point" value="{{old('point')}}">
                                        @if($errors->has('point'))
                                        <small class="text-danger">{{$errors->first('point')}}</small>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            <button class="btn btn-info waves-effect waves-float waves-light float-right"
                                type="submit">Submit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
<!-- Basic Inputs end -->

@endsection
@section('css')

@endsection
@section('js')

@endsection
@push('script')

@endpush