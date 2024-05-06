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
    <x-bread-crumb-component title='Create User' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Create User</h3>
                        </div>
                        <div class="card-body">
                            <form action="">
                                <div class="row">
                                    <div class="col-12">
                                        <x-forms.text label="User Name" inputName="user_name" placeholder="Enter User Name" :isRequired='true'  :isReadonly='false' defaultValue=""/>
                                    </div>
                                    <div class="col-12">
                                        <x-forms.password label="Password" inputName="password" placeholder="Enter Password" :isRequired='true'  :isReadonly='false' defaultValue=""/>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
