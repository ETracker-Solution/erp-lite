@extends('layouts.app')
@section('title')
User Edit
@endsection
@section('style')
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('admin/plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endsection
@section('content')
@php
$links = [
'Home'=>route('dashboard'),
'User Edit'=>''
]
@endphp
<x-breadcrumb title='User' :links="$links" />

<section class="content">

    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">User Edit</h3>
            <div class="card-tools">
                <a href="{{route('users.index')}}"><button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i> &nbsp;See List</button></a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <form action="{{ route('users.update',$user->id)}}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
        <div class="row">
            <div class="col-5">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">User Entry</h3>
                    </div>
                    <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label for="employee_id">Employee</label>
                                    <select name="employee" id="employee" class="form-control select2" required disabled>
                                        <option value="" selected>Select Employee</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee }}"
                                                {{ $employee->id == $user->employee_id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="employee_id">
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <x-forms.text label="Name" inputName="name" placeholder="Enter User Name" :isRequired='false'  :isReadonly='true' defaultValue="{{ $user->name }}"/>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <x-forms.email label="Email" inputName="email" placeholder="Enter Email" :isRequired='false' :isReadonly='true' defaultValue="{{ $user->email }}" />
                                </div>
                            </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <x-forms.password label="Password" inputName="password" placeholder="Enter Password" :isRequired='false'  :isReadonly='false' defaultValue=""/>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <x-forms.password label="Retype Password" inputName="password_confirmation" placeholder="Enter Password" :isRequired='false'  :isReadonly='false' defaultValue=""/>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-7">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">User Access</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                @foreach($data as $module => $permissions)
                                    <div class="form-group row callout callout-info align-items-center">
                                        <label for="inputEmail3" class="col-sm-2 col-form-label">{{ $module }}</label>
                                        <div class="col-sm-10">
                                            <div class="row">
                                                @foreach($permissions as $permission)
                                                <div class="col-6">
                                                    <div class="form-check form-switch form-check-inline">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" @if(in_array($permission->name, $userPermissions)) checked @endif value="{{ $permission->name }}">
                                                        <label class="form-check-label" for="flexSwitchCheckDefault">{{ $permission->display_name }}</label>
                                                    </div>
                                                </div>     
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-info waves-effect waves-float waves-light float-right"
                                type="submit">Submit
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </form>

    </div>
</section>



<!-- Main content -->
{{-- <section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Horizontal Form -->
                <div class="card card-info">
                    <div class="card-header">

                        <h3 class="card-title">User Edit</h3>
                        <div class="card-tools">
                            <a href="{{route('users.index')}}"><button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i> &nbsp;See List</button></a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form method="POST" action="{{ route('users.update',$user->id)}}" class="form form-horizontal" enctype="multipart/form-data">
                        @csrf
                        @method('put')

                        <div class="row">
                            <div class="col-6">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">User Entry</h3>
                                    </div>
                                    <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <label for="employee_id">Employee</label>
                                                    <select name="employee" id="employee" class="form-control select2" required>
                                                        <option value="" selected>Select Employee</option>
                                                        @foreach($employees as $employee)
                                                            <option value="{{ $employee }}">{{ $employee->id . ' - '. $employee->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="employee_id">
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <x-forms.text label="Name" inputName="name" placeholder="Enter User Name" :isRequired='false'  :isReadonly='true' defaultValue=""/>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <x-forms.email label="Email" inputName="email" placeholder="Enter Email" :isRequired='false' :isReadonly='true' defaultValue="" />
                                                </div>
                                            </div>
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <x-forms.password label="Password" inputName="password" placeholder="Enter Password" :isRequired='true'  :isReadonly='false' defaultValue=""/>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <x-forms.password label="Retype Password" inputName="password_confirmation" placeholder="Enter Password" :isRequired='true'  :isReadonly='false' defaultValue=""/>
                                            </div>
                                        </div>
            
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">User Access</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                @foreach($data as $module => $permissions)
                                                    <div class="form-group row callout callout-info align-items-center">
                                                        <label for="inputEmail3" class="col-sm-2 col-form-label">{{ $module }}</label>
                                                        <div class="col-sm-10">
                                                            @foreach($permissions as $permission)
                                                                <div class="form-check form-switch form-check-inline">
                                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}">
                                                                    <label class="form-check-label" for="flexSwitchCheckDefault">{{ $permission->display_name }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button class="btn btn-sm btn-primary waves-effect waves-float waves-light float-right"
                                                type="submit">Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
            
                        </div>

                </div>
                <!-- /.card -->
            </div>
            <div class="col-2"></div>
        </div>
        <!-- /.row -->

    </div><!-- /.container-fluid -->
</section> --}}
<!-- /.content -->
@endsection
@push('script')
<script>
        $('#employee').on('select2:select', function (e) {
        const user = JSON.parse(e.params.data.id);
        console.log(user)
        $('input[name=employee_id]').val(user.id)
        $('input[name=name]').val(user.name)
        $('input[name=email]').val(user.email)
    })
</script>
@endpush
