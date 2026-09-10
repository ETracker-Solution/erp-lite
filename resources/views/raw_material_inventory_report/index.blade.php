@extends('layouts.app')

@section('title', 'RM Inventory Report')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Store RM Module' => '',
            'RM Inventory Report' => '',
        ];
    @endphp
    <x-breadcrumb title="RM Inventory Report" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Raw Material Inventory Report</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                Choose filters, then generate a PDF summary as of the selected date.
                            </p>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="store_id">Store</label>
                                        <select name="store_id" id="store_id" class="form-control bSelect"
                                                v-model="store_id">
                                            <option value="">All / Select store</option>
                                            @foreach($stores as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="group_id">Group</label>
                                        <select name="group_id" id="group_id" class="form-control bSelect"
                                                v-model="group_id" @change="fetch_product">
                                            <option value="">All / Select group</option>
                                            @foreach($groups as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="item_id">Item</label>
                                        <select name="item_id" id="item_id" class="form-control bSelect"
                                                v-model="item_id">
                                            <option value="">All / Select item</option>
                                            <option :value="row.id" v-for="row in items" :key="row.id"
                                                    v-html="row.name"></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="as_on_date">As On Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="as_on_date" v-model="as_on_date">
                                    </div>
                                </div>
                            </div>

                            <hr class="mt-1 mb-3">

                            @include('partials.report_export_format')

                            <div class="small text-muted mb-2">Generate Report</div>
                            <div class="row">
                                @if ($isAdmin)
                                    <div class="col-md-6 mb-2">
                                        <button type="button" class="btn btn-dark btn-block"
                                                :disabled="pageLoading"
                                                @click="showReport('all_groups')">
                                            All Groups Summary
                                        </button>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <button type="button" class="btn btn-dark btn-block"
                                                :disabled="pageLoading"
                                                @click="showReport('single_group_item')">
                                            Single Group Items
                                        </button>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <button type="button" class="btn btn-dark btn-block"
                                                :disabled="pageLoading"
                                                @click="showReport('all_item')">
                                            All Items Summary
                                        </button>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <button type="button" class="btn btn-dark btn-block"
                                                :disabled="pageLoading"
                                                @click="showReport('store_group')">
                                            All Stores Summary
                                        </button>
                                    </div>
                                @endif
                                <div class="col-md-6 mb-2">
                                    <button type="button" class="btn btn-primary btn-block"
                                            :disabled="pageLoading"
                                            @click="showReport('store_group_item')">
                                        Store (+ optional Group / Item)
                                    </button>
                                </div>
                            </div>

                            <div class="text-center text-muted small mt-2" v-if="pageLoading">
                                Generating report…
                            </div>
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

@push('script')
    @include('partials.report_export_blob')
    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script src="{{ asset('vue-js/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            new Vue({
                el: '#vue_app',
                data: {
                    config: {
                        inventoryReportUrl: "{{ url('raw-materials-inventory-report') }}",
                        get_items_info_by_group_id_url: "{{ url('fetch-items-by-group-id') }}",
                    },
                    as_on_date: "{{ date('Y-m-d') }}",
                    group_id: '',
                    item_id: '',
                    store_id: '',
                    items: [],
                    export_format: 'pdf',
                    pageLoading: false,
                },
                methods: {
                    fetch_product: function () {
                        var vm = this;
                        vm.item_id = '';
                        vm.items = [];
                        if (!vm.group_id) {
                            vm.$nextTick(function () {
                                $('.bSelect').selectpicker('refresh');
                            });
                            return;
                        }
                        axios.get(this.config.get_items_info_by_group_id_url + '/' + vm.group_id)
                            .then(function (response) {
                                vm.items = response.data.products || [];
                                vm.$nextTick(function () {
                                    $('.bSelect').selectpicker('refresh');
                                });
                            })
                            .catch(function () {
                                toastr.error('Something went to wrong', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                            });
                    },
                    showReport: function (reportType) {
                        var vm = this;
                        if (reportType === 'single_group_item' && !vm.group_id) {
                            toastr.error('Please Select Group', {closeButton: true, progressBar: true});
                            return;
                        }
                        if (reportType === 'store_group_item' && !vm.store_id) {
                            toastr.error('Please Select Store', {closeButton: true, progressBar: true});
                            return;
                        }
                        if (!vm.as_on_date) {
                            toastr.error('Please Select As On Date', {closeButton: true, progressBar: true});
                            return;
                        }

                        vm.pageLoading = true;
                        axios.get(this.config.inventoryReportUrl + '/create', {
                            params: {
                                report_type: reportType,
                                as_on_date: vm.as_on_date,
                                group_id: vm.group_id || null,
                                item_id: vm.item_id || null,
                                store_id: vm.store_id || null,
                                export_format: vm.export_format,
                            },
                            responseType: 'blob',
                        }).then(function (response) {
                            if (!response.data || response.data.size === 0 || response.status === 204) {
                                toastr.error('No Data to Generate Report', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                vm.pageLoading = false;
                                return;
                            }
                            return window.handleReportBlobResponse(response, vm.export_format, 'rm-inventory-report')
                                .finally(function () { vm.pageLoading = false; });
                        }).catch(function () {
                            vm.pageLoading = false;
                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });
                        });
                    },
                },
                updated: function () {
                    $('.bSelect').selectpicker('refresh');
                },
                mounted: function () {
                    $('.bSelect').selectpicker({
                        liveSearch: true,
                        size: 8
                    });
                },
            });
        });
    </script>
@endpush
