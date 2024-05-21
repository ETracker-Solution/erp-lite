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
            'Employee Entry' => '',
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
                    <form action="{{ route('employees.store') }}" method="POST" class="" id="employeeForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_method" value="POST">
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
                                                       value="{{ old('employee_id') }}" name="employee_id"
                                                       id="employee_id">
                                                <span class="input-group-append">
                                                    <button type="button"
                                                            class="btn btn-info btn-flat" onclick="getEmployeeData()">Search</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="Name" inputName="name" placeholder="Enter Name"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="image">Employee Image</label>
                                            <input type="file" class="form-control" id="image"
                                                name="image">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.static-select label="Status" inputName="status" placeholder="Select One" :isRequired='true'  :isReadonly='false' defaultValue="" :options="['active','inactive']"/>
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
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="Nominee Name" inputName="nominee_name" placeholder="Nominee Name"
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
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="Nominee Relation" inputName="nominee_relation" placeholder="Nominee Relation"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="NID" inputName="nid" placeholder="Enter Your NID"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <x-forms.text label="Bank Account" inputName="bank_account" placeholder="Enter Your Bank Account"
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
                                        <x-forms.text label="Present Address" inputName="present_address" placeholder="Present Address"
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
                                        <x-forms.text label="Permanent Address" inputName="permanent_address" placeholder="Permanent Address"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.email label="Personal Email" inputName="personal_email" placeholder="Enter Personal Email"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.text label="Alternative Contact" inputName="alternative_phone" placeholder="Enter Alternative Contact"
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
                                        <x-forms.select label="Designation" inputName="designation_id" placeholder="Select One" :isRequired='true'  :isReadonly='false' defaultValue="" :options="$designations" optionId="id" optionValue="name"/>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.text label="Sallery" inputName="sallery" placeholder="Enter Sallery"
                                            :isRequired='true' :isReadonly='false' defaultValue="" />
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

<script>
    function getEmployeeData(){
        var employeeId = ($('input[name=employee_id]').val())
        if(!employeeId){
            toastr.error('Please Enter Employee ID')
                return
        }
        $.ajax({
                method: 'GET',
                url: "/employees/" + employeeId,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function (result) {
                   if(!result){
                    toastr.error('Invalid Employee Id')
                return
                   }
                    $('input[name=_method]').val('PUT')
                    $('input[name=name]').val(result.name)
                    $('input[name=father_name]').val(result.father_name)
                    $('input[name=mother_name]').val(result.mother_name)
                    $('input[name=nominee_name]').val(result.nominee_name)
                    $('input[name=nominee_relation]').val(result.nominee_relation)
                    $('select[name=blood_group]').val(result.blood_group)
                    $('input[name=nid]').val(result.nid)
                    $('input[name=dob]').val(result.dob)
                    $('input[name=bank_account]').val(result.bank_account)
                    $('input[name=present_address]').val(result.present_address)
                    $('input[name=permanent_address]').val(result.permanent_address)
                    $('input[name=email]').val(result.email)
                    $('input[name=personal_email]').val(result.personal_email)
                    $('input[name=phone]').val(result.phone)
                    $('input[name=alternative_phone]').val(result.alternative_phone)
                    $('select[name=department_id]').val(result.department_id)
                    $('select[name=designation_id]').val(result.designation_id)
                    $('select[name=outlet_id]').val(result.outlet_id)
                    $('input[name=sallery]').val(result.sallery)
                    $('input[name=joining_date]').val(result.joining_date)
                    $('input[name=confirm_date]').val(result.confirm_date)
                    $('select[name=status]').val(result.status)
                     var formElement =  $('#employeeForm')
                    formElement.attr('action',result.update_url)
                },
                
            });
    }
</script>
@endpush
