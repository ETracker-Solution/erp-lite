@extends('layouts.app')
@section('title')
    Employee
@endsection
@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
            'Home' => route('dashboard'),
            'Employee Create' => '',
        ];
    @endphp
    <x-breadcrumb title='Employee' :links="$links" />
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="{{ route('employees.store') }}" method="POST" class="">
                        @csrf
                        <!-- Horizontal Form -->
                        <div class="card card-info">
                            <div class="card-header">

                                <h3 class="card-title">Employee</h3>
                                <div class="card-tools">
                                    <a href="{{ route('employees.index') }}"><button class="btn btn-sm btn-primary"><i
                                                class="fa fa-list" aria-hidden="true"></i> &nbsp;See List</button></a>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.text label="Name" inputName="name" placeholder="Enter Name"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.email label="Email" inputName="email" placeholder="Enter Email"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.text label="Phone" inputName="phone" placeholder="Enter Phone"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <x-forms.text label="Address" inputName="address" placeholder="Enter Address"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="image">Employee Image</label>
                                            <input type="file" class="form-control" id="image"
                                                name="image">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Personal Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="Father Name" inputName="name" placeholder="Father Name"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="Nominee Name" inputName="name" placeholder="Nominee Name"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <x-forms.static-select label="Blood Group" inputName="blood_group" placeholder="Select One" :isRequired='true'  :isReadonly='false' defaultValue="" :options="['A+','A-','AB+','AB-','B+','B-','O+','O-']"/>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="date">Date of Birth</label>
                                            <div class="input-group date" id="reservationdate"
                                                data-target-input="nearest">
                                                <input type="text" name="date" value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
                                                    data-target="#reservationdate" />
                                                <div class="input-group-append" data-target="#reservationdate"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="Mother Name" inputName="name" placeholder="Mother Name"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="Nominee Relation" inputName="name" placeholder="Nominee Relation"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="NID" inputName="phone" placeholder="Enter Your NID"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="Bank Account" inputName="address" placeholder="Enter Your Bank Account"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>

                                </div>
                                
                            </div>
                        </div>

                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Contact Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.text label="Present Address" inputName="name" placeholder="Present Address"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.email label="Email" inputName="email" placeholder="Enter Email"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.text label="Mobile" inputName="phone" placeholder="Enter Mobile"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.text label="Permanent Address" inputName="address" placeholder="Permanent Address"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.email label="Personal Email" inputName="email" placeholder="Enter Personal Email"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.text label="Alternative Contact" inputName="phone" placeholder="Enter Alternative Contact"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Official Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.select label="Department" inputName="department_id" placeholder="Select One" :isRequired='true'  :isReadonly='false' defaultValue="" :options="$departments" optionId="id" optionValue="name"/>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.select label="Outlet" inputName="outlet_id" placeholder="Select One" :isRequired='true'  :isReadonly='false' defaultValue="" :options="$outlets" optionId="id" optionValue="name"/>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <div class="form-group">
                                            <label for="serial_no">Joining Date</label>
                                            <div class="input-group date" id="reservationdate"
                                                data-target-input="nearest">
                                                <input type="text" name="date" value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
                                                    data-target="#reservationdate" />
                                                <div class="input-group-append" data-target="#reservationdate"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.select label="Designation" inputName="designation_id" placeholder="Select One" :isRequired='true'  :isReadonly='false' defaultValue="" :options="$designations" optionId="id" optionValue="name"/>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.text label="Sallery" inputName="salllery" placeholder="Enter Sallery"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <div class="form-group">
                                            <label for="serial_no">Confirmation Date</label>
                                            <div class="input-group date" id="reservationdate"
                                                data-target-input="nearest">
                                                <input type="text" name="date" value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
                                                    data-target="#reservationdate" />
                                                <div class="input-group-append" data-target="#reservationdate"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary waves-effect waves-float waves-light float-right mb-2"
                            type="submit">Submit
                        </button>
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

@push('script')
@endpush
