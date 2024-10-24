@extends('layouts.app')

@section('title', 'Member Point Settings Edit')
@section('content')
@php
    $links = [
    'Home'=>route('dashboard'),
    'Member Point Settings list'=>route('member-points.index'),
    'Member Point Settings Edit'=>'',
    ]
    @endphp
    <x-breadcrumb title='Member Point Settings' :links="$links" />
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Member Point Settings Edit</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{route('member-points.update',encrypt($memberPoint->id))}}" method="POST"
                                class="" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-xl-4 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="member_type_id">Member Type</label>
                                            <select class="form-control select2" name="member_type_id"
                                                id="member_type_id">
                                                <option value="">Select One</option>
                                                @foreach ($memberTypes as $row)
                                                <option value="{{ $row->id }}" {{ $row->id ==
                                                    $memberPoint->member_type_id ? 'selected' : '' }}>
                                                    {{ $row->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('member_type_id'))
                                            <small class="text-danger">{{$errors->first('member_type_id')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- <div class="col-xl-4 col-md-8 col-12 mb-1">--}}
                                        {{-- <div class="form-group">--}}
                                            {{-- <label for="from_amount">From Amount</label>--}}
                                            {{-- <input type="number" class="form-control" id="from_amount"
                                                value="{{$memberPoint->from_amount}}" name="from_amount"
                                                placeholder="Enter From Amount">--}}
                                            {{-- @if($errors->has('from_amount'))--}}
                                            {{-- <small
                                                class="text-danger">{{$errors->first('from_amount')}}</small>--}}
                                            {{-- @endif--}}
                                            {{-- </div>--}}
                                        {{-- </div>--}}
                                    {{-- <div class="col-xl-4 col-md-8 col-12 mb-1">--}}
                                        {{-- <div class="form-group">--}}
                                            {{-- <label for="to_amount">To Amount</label>--}}
                                            {{-- <input type="number" class="form-control" id="to_amount"
                                                value="{{$memberPoint->to_amount}}" name="to_amount"
                                                placeholder="Enter to_amount">--}}

                                            {{-- @if($errors->has('to_amount'))--}}
                                            {{-- <small class="text-danger">{{$errors->first('to_amount')}}</small>--}}
                                            {{-- @endif--}}
                                            {{-- </div>--}}
                                        {{-- </div>--}}
                                    <div class="col-xl-4 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="to_amount">Per Amount</label>
                                            <input type="number" class="form-control" id="per_amount"
                                                value="{{$memberPoint->per_amount}}" name="per_amount"
                                                placeholder="Enter per_amount">

                                            @if($errors->has('per_amount'))
                                            <small class="text-danger">{{$errors->first('per_amount')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-8 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="point">Point</label>
                                            <input type="number" class="form-control" id="point"
                                                value="{{$memberPoint->point}}" name="point" placeholder="Enter Point">

                                            @if($errors->has('point'))
                                            <small class="text-danger">{{$errors->first('point')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-info waves-effect waves-float waves-light float-right"
                                    type="submit">Update</button>
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