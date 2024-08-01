@extends('layouts.app')
@section('title', 'Membership')
@section('content')
@php
$links = [
    'Home'=>route('dashboard'),
    'Loyalty Module'=>'',
    'Loyalty Entry'=>'',
    'Membership create'=>''
]
@endphp
<x-breadcrumb title='Membership create' :links="$links"/>
<section class="content">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-info">
                <div class="card-header">
                    <h4 class="card-title">Membership Create</h4>
                    <div class="card-tools">
                        <a href="{{route('memberships.index')}}">
                            <button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i>
                                &nbsp;See List
                            </button>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{route('memberships.store')}}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="col-xl-4 col-md-8 col-12 mb-1">
                                <div class="form-group">
                                    <label for="customer_id">Customer</label>
                                    <select class="select2 form-control" name="customer_id">
                                        <option value="" selected disabled>Select One</option>
                                        @forelse($customers as $row)
                                            <option
                                                value="{{ $row->id }}" {{ old('customer_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}
                                            </option>
                                        @empty
                                        @endforelse
                                    </select>
                                    @if($errors->has('customer_id'))
                                        <small class="text-danger">{{$errors->first('customer_id')}}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-8 col-12 mb-1">
                                <div class="form-group">
                                    <label for="member_type_id">Member Type</label>
                                    <select class="select2 form-control" name="member_type_id">
                                        <option value="" selected disabled>Select One</option>
                                        @forelse($memberTypes as $row)
                                            <option
                                                value="{{ $row->id }}" {{ old('member_type_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}
                                            </option>
                                        @empty
                                        @endforelse
                                    </select>
                                    @if($errors->has('member_type_id'))
                                        <small
                                            class="text-danger">{{$errors->first('member_type_id')}}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-8 col-12 mb-1">
                                <div class="form-group">
                                    <label for="membership_number">Membership Number</label>
                                    <input type="text" class="form-control" id="membership_number"
                                           name="membership_number" placeholder="Enter Membership Number"
                                           value="{{old('membership_number')}}">
                                    @if($errors->has('membership_number'))
                                        <small
                                            class="text-danger">{{$errors->first('membership_number')}}</small>
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
<!-- Basic Inputs end -->
</section>

@endsection
@section('css')

@endsection
@section('js')

@endsection
@push('script')

@endpush
