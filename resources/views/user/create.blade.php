@extends('layouts.app')
@section('title')
    User Entry
@endsection
@section('content')
    @php
        $links = [
       'Home'=>route('dashboard'),
       'User Entry'=>''
        ]
    @endphp
    <x-breadcrumb title='User Entry' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data">
                @csrf
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
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label for="employee_id">Outlet ID</label>
                                    <select name="outlet_id" id="outlet_id" class="form-control select2">
                                        <option value="" selected>Select Outlet</option>
                                        @foreach($outlets as $outlet)
                                            <option value="{{ $outlet }}">{{ $outlet->id . ' - '. $outlet->name }}</option>
                                        @endforeach
                                    </select>
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

        </form>

        </div>
    </section>
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
