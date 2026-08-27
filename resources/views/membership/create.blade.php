@extends('layouts.app')
@section('title', 'Membership Create')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Membership' => route('memberships.index'),
            'Create' => '',
        ];
    @endphp
    <x-breadcrumb title="Membership Create" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Create Membership</h3>
                            <div class="card-tools">
                                <a href="{{ route('memberships.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list" aria-hidden="true"></i> List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('memberships.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="customer_id">Customer</label>
                                            <select class="form-control" name="customer_id" id="customer_id">
                                                <option value="" selected disabled>Select One</option>
                                                @foreach($customers as $row)
                                                    <option value="{{ $row->id }}" {{ old('customer_id') == $row->id ? 'selected' : '' }}>
                                                        {{ $row->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('customer_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="member_type_id">Member Type</label>
                                            <select class="form-control" name="member_type_id" id="member_type_id">
                                                <option value="" selected disabled>Select One</option>
                                                @foreach($memberTypes as $row)
                                                    <option value="{{ $row->id }}" {{ old('member_type_id') == $row->id ? 'selected' : '' }}>
                                                        {{ $row->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('member_type_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-group">
                                            <label for="point">Point</label>
                                            <input type="number" class="form-control" id="point" name="point"
                                                   placeholder="Enter Point" value="{{ old('point') }}">
                                            @error('point')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-info float-right" type="submit">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
