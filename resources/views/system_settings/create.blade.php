@extends('layouts.app')
@section('title', 'System Setting Create')
@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'System Admin Module'=>'',
        'System Setting'=>'',
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
                                            @if(getSettingValue('fav_icon') )
                                                 <img src="{{ asset('upload').'/'.getSettingValue('fav_icon') }}" id="showImage" alt="AdminLTE Logo"
                                                class="brand-image img-circle elevation-3" class="p-4" height="100" width="100">
                                            @else
                                            <img class="p-4" height="150" width="200"
                                                src="{{ asset('admin/app-assets/dummy/dammy.jpg') }}" alt="">
                                            @endif

                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="title">Conpany Logo</label>
                                            <input type="file" class="form-control" id="logoImage" name="settings[company_logo]" value=""/>
                                            <p class="text-danger">Conpany Logo must be 100X90</p>
                                            @if(getSettingValue('company_logo') )
                                                 <img src="{{ asset('upload').'/'.getSettingValue('company_logo') }}" id="logoShowImage" alt="AdminLTE Logo"
                                                class="brand-image img-circle elevation-3" class="p-4" height="100" width="100">
                                            @else
                                            <img class="p-4" height="150" width="200"
                                                src="{{ asset('admin/app-assets/dummy/dammy.jpg') }}" alt="">
                                             @endif
                                        
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
                                                placeholder="Enter Company Email" value="{{ getSettingValue('company_email') }}">
                                            @if ($errors->has('company_email'))
                                                <small class="text-danger">{{ $errors->first('company_email') }}</small>
                                            @endif
                                        </div>
                                    </div>   
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="copyright_name">Copyright Name</label>
                                            <input type="text" class="form-control" name="settings[copyright_name]"
                                                placeholder="Enter Copyright Name" value="{{ getSettingValue('copyright_name') }}">
                                            @if ($errors->has('copyright_name'))
                                                <small class="text-danger">{{ $errors->first('copyright_name') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="copyright_url">Copyright URL</label>
                                            <input type="text" class="form-control" name="settings[copyright_url]"
                                                placeholder="Enter Copyright URL" value="{{ getSettingValue('copyright_url') }}">
                                            @if ($errors->has('copyright_url'))
                                                <small class="text-danger">{{ $errors->first('copyright_url') }}</small>
                                            @endif
                                        </div>
                                    </div>    
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="software_name">Software Name</label>
                                            <input type="text" class="form-control" name="settings[software_name]"
                                                placeholder="Enter Software Name" value="{{ getSettingValue('software_name') }}">
                                            @if ($errors->has('software_name'))
                                                <small class="text-danger">{{ $errors->first('software_name') }}</small>
                                            @endif
                                        </div>
                                    </div>            
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-info waves-effect waves-float waves-light float-right"
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