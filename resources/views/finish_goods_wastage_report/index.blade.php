@extends('layouts.app')
@section('title')
    FG Wastage Report
@endsection
@section('content')
    @php
        $links = [
       'Home'=>route('dashboard'),
       'Store FG Module'=>'',
       'Report'=>'',
       'FG Wastage Report'=>'',
        ]
    @endphp
    <x-breadcrumb title='FG Wastage Report' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <span v-if="pageLoading" class="categoryLoader">
                            <img src="{{ asset('loading.gif') }}" alt="loading">
                        </span>
                <div class="col-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">Select Parameters</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="store_id">Store</label>
                                        <select name="store_id" id="store_id" class="form-control" v-model="store_id">
                                            <option value="">Select a Store</option>
                                            <option :value="row.id" v-for="row in stores"
                                            >@{{ row.id + ' - ' + row.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
{{--                                <div class="col-12">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="group_id">Group</label>--}}
{{--                                        <select name="group_id" id="group_id" @change="fetch_product"--}}
{{--                                                class="form-control" v-model="group_id">--}}
{{--                                            <option value="">Select a Group</option>--}}
{{--                                            <option :value="row.id" v-for="row in groups"--}}
{{--                                            >@{{ row.id + ' - ' + row.name }}--}}
{{--                                            </option>--}}
{{--                                        </select>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-12">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="item_id">Item</label>--}}
{{--                                        <select name="item_id" id="item_id"--}}
{{--                                                class="form-control bSelect" v-model="item_id"--}}
{{--                                                @change="get_product_info">--}}
{{--                                            <option value="">Select one</option>--}}
{{--                                            <option :value="row.id" v-for="row in items"--}}
{{--                                            >@{{ row.id + ' - ' + row.name }}--}}
{{--                                            </option>--}}
{{--                                        </select>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                            </div>
                        </div>
                    </div>
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">Select Date Range</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
{{--                                <div class="col-12">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="">As On Date</label>--}}
{{--                                        <vuejs-datepicker v-model="from_date" name="as_on_date"--}}
{{--                                                          placeholder="Select date"></vuejs-datepicker>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">From Date</label>
                                        <vuejs-datepicker v-model="from_date" name="from_date"
                                                          placeholder="Select date"></vuejs-datepicker>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">To Date</label>
                                        <vuejs-datepicker v-model="to_date" name="to_date"
                                                          placeholder="Select date"></vuejs-datepicker>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">Finish Goods Wastage Report</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center">
                                        <button class="btn btn-sm btn-dark w-50 mb-2"
                                                @click="showReport('all_summary')">
                                            Show All Summary
                                        </button>
                                        @if(\auth()->user() && \auth()->user()->employee && !\auth()->user()->employee->outlet_id)
{{--                                            <button class="btn btn-sm btn-dark w-50 mb-2"--}}
{{--                                                    @click="showReport('all_groups')">--}}
{{--                                                Show All Groups Quantity Summary--}}
{{--                                            </button>--}}

{{--                                            <button class="btn btn-sm btn-dark w-50 mb-2"--}}
{{--                                                    @click="showReport('single_group_item')">Show Single Group Item--}}
{{--                                                Quantity--}}
{{--                                                Summary--}}
{{--                                            </button>--}}
{{--                                            <button class="btn btn-sm btn-dark w-50 mb-2"--}}
{{--                                                    @click="showReport('all_item')">--}}
{{--                                                Show All Item Quantity Summary--}}
{{--                                            </button>--}}
                                            {{--                                        <button class="btn btn-sm btn-dark w-50 mb-2"--}}
                                            {{--                                                @click="showReport('single_item')">Show Single Item Quantity Summary--}}
                                            {{--                                        </button>--}}
{{--                                            <button class="btn btn-sm btn-dark w-50 mb-2"--}}
{{--                                                    @click="showReport('store_group')">Show All Store Quantity Summary--}}
{{--                                            </button>--}}
                                        @endif
{{--                                        <button class="btn btn-sm btn-dark w-50 mb-2"--}}
{{--                                                @click="showReport('store_group_item')">Show Single Store + Group + Item--}}
{{--                                            Quantity Summary--}}
{{--                                        </button>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('style')
    <style>
        .categoryLoader {
            position: absolute;
            top: 50%;
            right: 40%;
            transform: translate(-50%, -50%);
            color: red;
            z-index: 999;
        }

        input[placeholder="Select date"] {
            display: block;
            width: 100%;
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            box-shadow: inset 0 0 0 transparent;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('vue-js/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endpush
@push('script')
    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script src="{{ asset('vue-js/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="https://cms.diu.ac/vue/vuejs-datepicker.min.js"></script>
    <script>
        $(document).ready(function () {

            var vue = new Vue({
                el: '#vue_app',
                data: {
                    config: {
                        wastageReportUrl: "{{ url('finish-goods-wastage-report') }}",
                        initial_info_url: "{{ url('finish-goods-opening-balances-initial-info') }}",
                        get_items_info_by_group_id_url: "{{ url('fetch-items-by-group-id') }}",
                        get_item_info_url: "{{ url('fetch-item-info') }}",
                    },
                    from_date: new Date(),
                    to_date: new Date(),
                    group_id: '',
                    item_id: '',
                    store_id: '',
                    items: [],
                    pageLoading: false,
                    groups: [],
                    stores: []

                },
                components: {
                    vuejsDatepicker,
                },
                computed: {},
                methods: {
                    fetch_product() {
                        var vm = this;
                        var slug = vm.group_id;
                        if (slug) {
                            vm.pageLoading = true;
                            axios.get(this.config.get_items_info_by_group_id_url + '/' + slug).then(function (response) {
                                vm.items = response.data.products;
                                vm.pageLoading = false;
                            }).catch(function (error) {
                                toastr.error('Something went to wrong', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            });
                        }
                    },
                    get_product_info() {
                        const vm = this;
                        if (!vm.item_id) {
                            toastr.error('Please Select Item', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        } else {
                            const slug = vm.item_id;
                            if (slug) {
                                vm.pageLoading = true;
                                axios.get(this.config.get_item_info_url + '/' + slug).then(function (response) {
                                    let item_info = response.data;
                                    vm.unit = item_info.unit ? item_info.unit.name : '';
                                    vm.pageLoading = false;
                                }).catch(function (error) {
                                    toastr.error('Something went to wrong', {
                                        closeButton: true,
                                        progressBar: true,
                                    });
                                    return false;
                                });
                            }

                        }

                    },
                    backAsStarting() {
                        const vm = this;
                        vm.isEditMode = false
                        vm.date = new Date()
                        vm.group_id = ''
                        vm.item_id = ''
                        vm.unit = ''
                        vm.store_id = ''
                        vm.items = []
                        vm.quantity = ''
                        vm.rate = ''
                        vm.pageLoading = false
                        vm.remarks = ''
                        vm.editableItem = ''
                    },
                    get_initial_data() {
                        const vm = this;
                        vm.pageLoading = true;
                        axios.get(this.config.initial_info_url).then(function (response) {
                            vm.groups = response.data.groups;
                            vm.stores = response.data.stores;
                            vm.next_id = response.data.next_id;
                            vm.pageLoading = false;
                        }).catch(function (error) {
                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });
                    },
                    showReport(reportType) {
                        const vm = this;
                        if (reportType === 'all_summary') {
                            if (!vm.store_id) {
                                toastr.error('Please Select store', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            }
                        } if (reportType === 'single_group_item') {
                            if (!vm.group_id) {
                                toastr.error('Please Select Group', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            }
                        }
                        if (reportType === 'store_group_item') {
                            if (!vm.store_id) {
                                toastr.error('Please Select Store', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            }
                        }

                        vm.pageLoading = true;
                        axios.get(this.config.wastageReportUrl + '/create', {
                            params: {
                                report_type: reportType,
                                from_date: vm.from_date,
                                to_date: vm.to_date,
                                group_id: vm.group_id,
                                item_id: vm.item_id,
                                store_id: vm.store_id,
                            },
                            responseType: 'blob',
                        }).then(function (response) {
                            const blob = new Blob([response.data], {
                                type: 'application/pdf'
                            });
                            const url = window.URL.createObjectURL(blob);
                            window.open(url)
                            vm.pageLoading = false;
                        }).catch(function (error) {
                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });
                    }
                },

                updated() {
                    $('.bSelect').selectpicker('refresh');
                },
                mounted() {
                    this.get_initial_data()
                }
            });
            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });
        });
    </script>
@endpush
