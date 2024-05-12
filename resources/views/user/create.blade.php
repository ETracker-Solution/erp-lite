@extends('layouts.app')
@section('title')
    Create User
@endsection
@section('content')
    @php
        $links = [
       'Home'=>route('dashboard'),
       'Create User'=>''
        ]
    @endphp
    <x-breadcrumb title='Create User' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data">
                @csrf
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Create User</h3>
                        </div>
                        <div class="card-body">
                            
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.text label="Name" inputName="name" placeholder="Enter User Name" :isRequired='true'  :isReadonly='false' defaultValue=""/>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.email label="Email" inputName="email" placeholder="Enter Email" :isRequired='true' :isReadonly='false' defaultValue="" />
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <x-forms.password label="Password" inputName="password" placeholder="Enter Password" :isRequired='true'  :isReadonly='false' defaultValue=""/>
                                    </div>
                                </div>
                               
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">User Access</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($data as $module => $permissions)
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <div class="card card-info">
                                        <div class="card-header text-center">
                                            <h5 class="card-title">{{ $module }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="card-body">
                                                @foreach($permissions as $permission)
                                                    <div class="">
                                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}">
                                                        <span>{{ $permission->display_name }}</span>
                                                    </div>
                                                @endforeach
                                            </div>  
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary waves-effect waves-float waves-light float-right"
            type="submit">Submit
        </button>
        </form>

        </div>
    </section>
@endsection
