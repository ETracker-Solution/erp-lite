@extends('layouts.app')

@section('title', 'FG Wastage Report')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Store FG Module' => '',
            'Report' => '',
            'FG Wastage Report' => '',
        ];
    @endphp
    <x-breadcrumb title="FG Wastage Report" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Finish Goods Wastage Report</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                Choose store / date range, then generate a PDF. Store Wise requires a store.
                            </p>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="store_id">Store</label>
                                        <select name="store_id" id="store_id" class="form-control bSelect"
                                                v-model="store_id">
                                            <option value="">Select a store</option>
                                            @foreach($stores as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="from_date">From Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="from_date" v-model="from_date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="to_date">To Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="to_date" v-model="to_date">
                                    </div>
                                </div>
                            </div>

                            <hr class="mt-1 mb-3">

                            <div class="small text-muted mb-2">Generate PDF</div>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <button type="button" class="btn btn-dark btn-block"
                                            :disabled="pageLoading"
                                            @click="showReport('Store Wise Summary')">
                                        Store Wise Summary
                                    </button>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <button type="button" class="btn btn-dark btn-block"
                                            :disabled="pageLoading"
                                            @click="showReport('Product Wise')">
                                        Product Wise
                                    </button>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <button type="button" class="btn btn-dark btn-block"
                                            :disabled="pageLoading"
                                            @click="showReport('All Store')">
                                        All Store
                                    </button>
                                </div>
                            </div>
                            <div v-if="pageLoading" class="text-muted small mt-2">Generating PDF…</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('vue-js/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endpush

@push('js_scripts')
    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script src="{{ asset('vue-js/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script>
        $(function () {
            new Vue({
                el: '#vue_app',
                data: {
                    config: {
                        wastageReportUrl: @json(url('finish-goods-wastage-report')),
                    },
                    from_date: @json(date('Y-m-d')),
                    to_date: @json(date('Y-m-d')),
                    store_id: '',
                    pageLoading: false,
                },
                methods: {
                    showReport: function (reportType) {
                        var vm = this;

                        if (reportType === 'Store Wise Summary' && !vm.store_id) {
                            toastr.error('Please Select store', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return;
                        }

                        vm.pageLoading = true;
                        axios.get(this.config.wastageReportUrl + '/create', {
                            params: {
                                report_type: reportType,
                                from_date: vm.from_date,
                                to_date: vm.to_date,
                                store_id: vm.store_id || '',
                            },
                            responseType: 'blob',
                        }).then(function (response) {
                            if (response.status === 204 || !response.data || response.data.size === 0) {
                                toastr.info('No data for selected filters', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                vm.pageLoading = false;
                                return;
                            }
                            var blob = new Blob([response.data], {type: 'application/pdf'});
                            var url = window.URL.createObjectURL(blob);
                            window.open(url);
                            vm.pageLoading = false;
                        }).catch(function (error) {
                            var message = 'Something went wrong';
                            if (error.response && error.response.data) {
                                var reader = new FileReader();
                                reader.onload = function () {
                                    toastr.error(reader.result || message, {
                                        closeButton: true,
                                        progressBar: true,
                                    });
                                };
                                reader.readAsText(error.response.data);
                            } else {
                                toastr.error(message, {
                                    closeButton: true,
                                    progressBar: true,
                                });
                            }
                            vm.pageLoading = false;
                        });
                    },
                },
                updated: function () {
                    $('.bSelect').selectpicker('refresh');
                },
                mounted: function () {
                    $('.bSelect').selectpicker({
                        liveSearch: true,
                        size: 5
                    });
                },
            });
        });
    </script>
@endpush
