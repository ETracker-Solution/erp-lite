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
                    <div class="card card-info">
                        <div class="card-header">
    
                            <h3 class="card-title">Employee</h3>
                            <div class="card-tools">
                                <a href="{{ route('employees.index') }}"><button class="btn btn-sm btn-primary"><i
                                            class="fa fa-list" aria-hidden="true"></i> &nbsp;See List</button></a>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('employees.update',$employee->id) }}" method="POST" class="" id="employeeForm" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <!-- Horizontal Form -->
                        <div class="card">
                            <!-- form start -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="employee_id">Employee ID</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control input-sm"
                                                       value="{{ $employee->employee_id }}"
                                                       id="employee_id" readonly>
                                                <span class="input-group-append">
                                                    <button type="button"
                                                            class="btn btn-info btn-flat" onclick="getEmployeeData()">Search</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="Name" inputName="name" placeholder="Enter Name"
                                            :isRequired='true' :isReadonly='false' defaultValue="{{ $employee->name }}" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="image">Employee Image</label>
                                            <input type="file" class="form-control" id="image"
                                                name="image">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.static-select label="Status" inputName="status" placeholder="Select One" :isRequired='true'  :isReadonly='false' :defaultValue="$employee ? $employee->status : ''" :options="['active','inactive']"/>
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
                                        <x-forms.text label="Father Name" inputName="father_name" placeholder="Father Name"
                                            :isRequired='true' :isReadonly='false' defaultValue="{{ $employee->father_name }}" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="Nominee Name" inputName="nominee_name" placeholder="Nominee Name"
                                            :isRequired='true' :isReadonly='false' defaultValue="{{ $employee->nominee_name }}" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <x-forms.static-select label="Blood Group" inputName="blood_group" placeholder="Select One" :isRequired='true'  :isReadonly='false' :defaultValue="$employee ? $employee->blood_group : ''" :options="['A+','A-','AB+','AB-','B+','B-','O+','O-']"/>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="date">Date of Birth</label>
                                            <div class="input-group date" id="reservationdate"
                                                data-target-input="nearest">
                                                <input type="text" name="dob" value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
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
                                        <x-forms.text label="Mother Name" inputName="mother_name" placeholder="Mother Name"
                                            :isRequired='true' :isReadonly='false' defaultValue="{{ $employee->mother_name }}" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="Nominee Relation" inputName="nominee_relation" placeholder="Nominee Relation"
                                            :isRequired='true' :isReadonly='false' defaultValue="{{ $employee->nominee_relation }}" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="NID" inputName="nid" placeholder="Enter Your NID"
                                            :isRequired='true' :isReadonly='false' defaultValue="{{ $employee->nid }}" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="Bank Account" inputName="bank_account" placeholder="Enter Your Bank Account"
                                            :isRequired='true' :isReadonly='false' defaultValue="{{ $employee->bank_account }}" />
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
                                        <x-forms.text label="Present Address" inputName="present_address" placeholder="Present Address"
                                            :isRequired='true' :isReadonly='false' defaultValue="{{ $employee->present_address }}" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.email label="Email" inputName="email" placeholder="Enter Email"
                                            :isRequired='true' :isReadonly='false' defaultValue="{{ $employee->email }}" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.text label="Mobile" inputName="phone" placeholder="Enter Mobile"
                                            :isRequired='true' :isReadonly='false' defaultValue="{{ $employee->phone }}" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.text label="Permanent Address" inputName="permanent_address" placeholder="Permanent Address"
                                            :isRequired='true' :isReadonly='false' defaultValue="{{ $employee->permanent_address }}" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.email label="Personal Email" inputName="personal_email" placeholder="Enter Personal Email"
                                            :isRequired='true' :isReadonly='false' defaultValue="{{ $employee->personal_email }}" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.text label="Alternative Contact" inputName="alternative_phone" placeholder="Enter Alternative Contact"
                                            :isRequired='true' :isReadonly='false' defaultValue="{{ $employee->alternative_phone }}" />
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
                                        <x-forms.select label="Department" inputName="department_id" placeholder="Select One" :isRequired='true'  :isReadonly='false' :defaultValue="$employee ? $employee->department_id : ''" :options="$departments" optionId="id" optionValue="name"/>
                                    </div>
                                    
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <div class="form-group">
                                            <label for="joining_date">Joining Date</label>
                                            <div class="input-group date" id="reservationdate1"
                                                data-target-input="nearest">
                                                <input type="text" name="joining_date" value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
                                                    data-target="#reservationdate1" />
                                                <div class="input-group-append" data-target="#reservationdate1"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.select label="Designation" inputName="designation_id" placeholder="Select One" :isRequired='true'  :isReadonly='false' :defaultValue="$employee ? $employee->designation_id : ''" :options="$designations" optionId="id" optionValue="name"/>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.text label="Salery" inputName="salary" placeholder="Enter Salery"
                                            :isRequired='true' :isReadonly='false' defaultValue="{{ $employee->salary }}" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <div class="form-group">
                                            <label for="confirm_date">Confirmation Date</label>
                                            <div class="input-group date" id="reservationdate2"
                                                data-target-input="nearest">
                                                <input type="text" name="confirm_date" value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
                                                    data-target="#reservationdate2" />
                                                <div class="input-group-append" data-target="#reservationdate2"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-md-4 col-4 mb-1">
                                        @php
                                        $user_of = [
                                        (object) [
                                        'key'=>'factory',
                                        'value'=>'Factory'
                                        ] ,
                                        (object) [
                                        'key'=>'outlet',
                                        'value'=>'Outlet'
                                        ] ,
                                        ];
                                        @endphp
                                        <x-forms.select label="User Of" inputName="user_of" placeholder="Select One"
                                            :isRequired='true' :isReadonly='false' :defaultValue="$employee ? $employee->user_of : ''" :options="$user_of"
                                            optionId="key" optionValue="value" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" id="outletDropdown" hidden>
                                        <x-forms.select label="Outlet" inputName="outlet_id" placeholder="Select One"
                                            :isRequired='false' :isReadonly='false' :defaultValue="$employee ? $employee->outlet_id : ''" :options="$outlets"
                                            optionId="id" optionValue="name" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" id="factoryDropdown" hidden>
                                        <x-forms.select label="Factory" inputName="factory_id" placeholder="Select One"
                                            :isRequired='false' :isReadonly='false' :defaultValue="$employee ? $employee->factory_id : ''" :options="$factories"
                                            optionId="id" optionValue="name" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-info waves-effect waves-float waves-light float-right mb-2"
                            type="submit">Update
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
    <script>

        $(document).ready(function() {
            var docType = $('select[name=user_of]').val();
            console.log('Auto-selected value:', docType);

            if (docType === 'factory') {
                $('#factoryDropdown').prop('hidden', false)
                $('#outletDropdown').prop('hidden', true)

                $('select[name=doc_id]').val('')
            } else if (docType === 'outlet') {
                $('#factoryDropdown').prop('hidden', true)
                $('#outletDropdown').prop('hidden', false)
            } else {
                $('#factoryDropdown').prop('hidden', true)
                $('#outletDropdown').prop('hidden', true)
            }
        });

        $('select[name=user_of]').on('select2:select', function (e) {
            const docType = e.params.data.id;
            $('select[name=factory_id]').val('')
            $('select[name=outlet_id]').val('')
            if (docType === 'factory') {
                $('#factoryDropdown').prop('hidden', false)
                $('#outletDropdown').prop('hidden', true)

                $('select[name=doc_id]').val('')
            } else if (docType === 'outlet') {
                $('#factoryDropdown').prop('hidden', true)
                $('#outletDropdown').prop('hidden', false)
            } else {
                $('#factoryDropdown').prop('hidden', true)
                $('#outletDropdown').prop('hidden', true)
            }
        })
    </script>
@endpush
