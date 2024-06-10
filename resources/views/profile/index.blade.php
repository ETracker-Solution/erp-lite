@extends('layouts.app')
@section('title')
    Profile
@endsection
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row mt-2">
            <div class="col-md-3">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            @if (isset(Auth::guard('web')->user()->employee->image) && file_exists('upload/'.Auth::guard('web')->user()->employee->image))
                                    <img src="{{ asset('/upload/'.Auth::guard('web')->user()->employee->image) }}" class="profile-user-img img-fluid img-circle"
                                    alt="" style="height: 100px">
                            @else
                                <img src="{{ asset('admin/app-assets/dummy/dammy.jpg') }}"
                                class="profile-user-img img-fluid img-circle" alt="">
                            @endif
                        </div>
                        <h3 class="profile-username text-center">{{ $adminProfile->name }}</h3>
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Phone</b> <a class="float-right">{{ Auth::guard('web')->user()->employee->phone }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Email</b> <a class="float-right">{{ $adminProfile->email }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Address</b> <a class="float-right">{{ Auth::guard('web')->user()->employee->present_address }}</a>
                            </li>
                        </ul>
                        {{-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> --}}
                    </div>

                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-pane" id="settings">
                            <form action="{{ route('profile.update-admin') }}" method="POST" class="form-horizontal">
                                @csrf
                                <div class="form-group row">
                                    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="name" value="{{ $adminProfile->name ?? '' }}" id="inputName" placeholder="Name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" Value="{{ $adminProfile->email ?? '' }}" id="inputEmail"
                                            placeholder="Email" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputPassword" class="col-sm-2 col-form-label"> Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" id="inputPassword" class="form-control" placeholder="Enter Password" value=""
                                            name="password">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputRepassword" class="col-sm-2 col-form-label"> Retype Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" id="inputRepassword" class="form-control" placeholder="Enter Password" value=""
                                            name="password_confirmation">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-10">
                                        <button type="submit" class="btn btn-danger">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
</section>
@endsection