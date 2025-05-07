@extends('layouts.app')
@section('title', 'Vat Config Create')
@section('content')
    @php
$links = [
    'Home' => route('dashboard'),
    'System Admin Module' => '',
    'Vat Config' => '',
    'Vat Config Entry' => ''
]
    @endphp
    <x-breadcrumb title='Vat Config Entry' :links="$links"/>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('vat-config.store') }}" method="POST" class=""
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Vat Config Entry</h4>
                            </div>
                            <hr style="margin: 0;">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="">Global Vat+SD Type</label>
                                            <select name="settings[global_vat_type]" id="" class="form-control">
                                                    <option value="">Select Type</option>
                                                    <option value="excluding" {{ 'excluding' == getSettingValue('global_vat_type') ? 'selected' : '' }}>Excluding</option>
                                                    <option value="including" {{ 'including' == getSettingValue('global_vat_type') ? 'selected' : '' }}>Including</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="global_vat_amount">Global Vat Amount(%)</label>
                                            <input type="text" class="form-control" name="settings[global_vat_amount]"
                                            placeholder="Enter Global Vat Amount" value="{{ getSettingValue('global_vat_amount') }}">
                                        @if ($errors->has('global_vat_amount'))
                                            <small class="text-danger">{{ $errors->first('global_vat_amount') }}</small>
                                        @endif                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="global_sd_amount">Global SD Amount(%)</label>
                                            <input type="text" class="form-control" name="settings[global_sd_amount]"
                                            placeholder="Enter Global SD Amount" value="{{ getSettingValue('global_sd_amount') }}">
                                        @if ($errors->has('global_sd_amount'))
                                            <small class="text-danger">{{ $errors->first('global_sd_amount') }}</small>
                                        @endif                                        </div>
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
