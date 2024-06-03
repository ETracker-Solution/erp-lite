@extends('layouts.app')
@section('title', 'System Setting Create')
@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'System Setting Entry'=>''
        ]
    @endphp
    <x-breadcrumb title='System Setting Entry' :links="$links"/>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('system-settings.store') }}" method="POST" class=""
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">System Setting Entry</h4>
                            </div>
                            <hr style="margin: 0;">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="title">Fav Icon</label>
                                            <input type="file" class="form-control" id="image" name="settings[fav_icon]" value=""/>
                                            <p class="text-danger">Conpany Fav Icon must be 16X16</p>
                                            <img class="p-4" height="150" width="200" id="showImage"
                                                src="{{ asset('admin/app-assets/dummy/dammy.jpg') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="title">Conpany Logo</label>
                                            <input type="file" class="form-control" id="logoImage" name="settings[company_logo]" value=""/>
                                            <p class="text-danger">Conpany Logo must be 100X90</p>
                                            <img class="p-4" height="150" width="200" id="logoShowImage"
                                                src="{{ asset('admin/app-assets/dummy/dammy.jpg') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="company_name">Company Name</label>
                                            <input type="text" class="form-control" name="settings[company_name]"
                                                placeholder="Enter Company Name" value="{{ getSettingValue('company_name') }}">
                                            @if ($errors->has('company_name'))
                                                <small class="text-danger">{{ $errors->first('company_name') }}</small>
                                            @endif
                                        </div>
                                    </div> 
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="company_address">Conpany Address</label>
                                            <textarea class="form-control" name="settings[company_address]" cols="30" rows="1">{{ getSettingValue('company_address') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="company_phone">Company Phone</label>
                                            <input type="text" class="form-control" name="settings[company_phone]"
                                                placeholder="Enter Company Name" value="{{ getSettingValue('company_phone') }}">
                                            @if ($errors->has('company_phone'))
                                                <small class="text-danger">{{ $errors->first('company_phone') }}</small>
                                            @endif
                                        </div>
                                    </div> 
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="company_email">Company Email</label>
                                            <input type="email" class="form-control" name="settings[company_email]"
                                                placeholder="Enter Company Name" value="{{ getSettingValue('company_email') }}">
                                            @if ($errors->has('company_email'))
                                                <small class="text-danger">{{ $errors->first('company_email') }}</small>
                                            @endif
                                        </div>
                                    </div>                   
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-primary waves-effect waves-float waves-light float-right"
                                    type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>   
    </section>
@endsection
@push('script')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>    
    <script>
        $(document).ready(function() {
            $('#image').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#showImage').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#logoImage').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#logoShowImage').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
        });
    </script>

@endpush