@extends('layouts.app')
@section('title','FG Production report')

@section('content')
    <section class="content-header">
        @php
            $links = [
            'Home'=>route('dashboard'),
            'Production Report'=>''
            ]
        @endphp
        <x-bread-crumb-component title='Production Report' :links="$links"/>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title">Production Report</h3>
                            <div class="card-tools">
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="fp-range" class="font-weight-bold">DATE RANGE</label>
                                    <input type="text" id="fp-range" class="form-control flatpickr-range"
                                           placeholder="YYYY-MM-DD to YYYY-MM-DD" name="date_range"/>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="fp-range" class="font-weight-bold">Store</label>
                                    <select name="store_id" id="store_id" class="form-control select2">
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-md-6 form-group">
                                    <div class="row">
                                        <div class="col-md-12 form-group border-bottom pb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>All Production Consumption</span>
                                                <div>
                                                    <a href="#" class="btn btn-sm btn-primary report-btn" data-type="all" data-export="pdf"><i class="fa fa-file-pdf"></i> PDF</a>
                                                    <a href="#" class="btn btn-sm btn-success report-btn" data-type="all" data-export="excel"><i class="fa fa-file-excel"></i> Excel</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-group border-bottom pb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Pre-Order wise Consumption</span>
                                                <div>
                                                    <a href="#" class="btn btn-sm btn-primary report-btn" data-type="preorder" data-export="pdf"><i class="fa fa-file-pdf"></i> PDF</a>
                                                    <a href="#" class="btn btn-sm btn-success report-btn" data-type="preorder" data-export="excel"><i class="fa fa-file-excel"></i> Excel</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-group border-bottom pb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Without Pre-Order wise Consumption</span>
                                                <div>
                                                    <a href="#" class="btn btn-sm btn-primary report-btn" data-type="without_preorder" data-export="pdf"><i class="fa fa-file-pdf"></i> PDF</a>
                                                    <a href="#" class="btn btn-sm btn-success report-btn" data-type="without_preorder" data-export="excel"><i class="fa fa-file-excel"></i> Excel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <div class="row">
                                        <div class="col-md-12 form-group border-bottom pb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Total Production</span>
                                                <div>
                                                    <a href="#" class="btn btn-sm btn-primary report-btn" data-type="total_production" data-export="pdf"><i class="fa fa-file-pdf"></i> PDF</a>
                                                    <a href="#" class="btn btn-sm btn-success report-btn" data-type="total_production" data-export="excel"><i class="fa fa-file-excel"></i> Excel</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-group border-bottom pb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Total Consumption</span>
                                                <div>
                                                    <a href="#" class="btn btn-sm btn-primary report-btn" data-type="total_consumption" data-export="pdf"><i class="fa fa-file-pdf"></i> PDF</a>
                                                    <a href="#" class="btn btn-sm btn-success report-btn" data-type="total_consumption" data-export="excel"><i class="fa fa-file-excel"></i> Excel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('css')
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker/app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker/app-assets/css/plugins/forms/pickers/form-pickadate.css') }}">
@endsection
@push('style')
@endpush
@section('js')
    <script src="{{ asset('datepicker/app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('datepicker/app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('datepicker/app-assets/vendors/js/pickers/pickadate/picker.time.js') }}"></script>
    <script src="{{ asset('datepicker/app-assets/vendors/js/pickers/pickadate/legacy.js') }}"></script>
    <script src="{{ asset('datepicker/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('datepicker/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
@endsection
@push('script')
    <script>
        document.querySelectorAll('.report-btn').forEach(button => {
            button.addEventListener('click', function () {

                let dateRange = document.getElementById('fp-range').value;
                let storeId   = document.getElementById('store_id').value;
                let type      = this.dataset.type;
                let exportType = this.dataset.export;

                if (!dateRange || !storeId) {
                    alert('Please select Date Range and Store');
                    return;
                }

                let url = "{{ route('production.reports') }}" +
                    "?type=" + type +
                    "&date_range=" + encodeURIComponent(dateRange) +
                    "&store_id=" + storeId +
                    "&export_type=" + exportType;

                window.open(url, '_blank');
            });
        });
    </script>


@endpush
