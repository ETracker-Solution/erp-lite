@extends('layouts.app')
@section('title')
    Sale Report
@endsection
@section('content')
    @php
        $links = [
       'Home'=>route('dashboard'),
       'Sales Module'=>'',
       'Sales Report'=>'',
        ]
    @endphp
    <x-breadcrumb title='Sales Report' :links="$links"/>

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
                                        <label for="store_id">Outlet</label>
                                        <select name="store_id" id="store_id" class="form-control" v-model="store_id">
                                            <option value="">Select a Outlet</option>
                                            <option :value="row.id" v-for="row in stores"
                                            >@{{ row.id + ' - ' + row.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="group_id">Group</label>
                                        <select name="group_id" id="group_id" class="form-control" v-model="group_id"
                                                @change="getItemsByGroup">
                                            <option value="">Select a Group</option>
                                            <option :value="row.id" v-for="row in groups"
                                            >@{{ row.id + ' - ' + row.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="item_id">Item</label>
                                        <select name="item_id" id="item_id" class="form-control" v-model="item_id">
                                            <option value="">Select a Item</option>
                                            <option :value="row.id" v-for="row in items"
                                            >@{{ row.id + ' - ' + row.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 border-bottom mb-3">
                                    <div class="form-group">
                                        <label for="customer_id">Customer</label>
                                        <select name="customer_id" id="customer_id" class="form-control"
                                                v-model="customer_id">
                                            <option value="">Select a Customer</option>
                                            <option :value="row.id" v-for="row in customers"
                                            >@{{ row.id + ' - ' + row.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <hr>
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
                                <hr>
                                {{--                                <div class="col-12">--}}
                                {{--                                    <div class="form-group">--}}
                                {{--                                        <label for="">As On Date</label>--}}
                                {{--                                        <vuejs-datepicker v-model="as_on_date" name="as_on_date"--}}
                                {{--                                                          placeholder="Select date"></vuejs-datepicker>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">Sales Report</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center">
                                        <button class="btn btn-sm btn-dark w-50 mb-2"
                                                @click="showReport('All Sales Record')">
                                            Show All Sales Record
                                        </button>
                                        <button class="btn btn-sm btn-dark w-50 mb-2"
                                                @click="showReport('Item Wise Sales Summary')">Show Item Wise Sales
                                            Summary
                                        </button>
                                        @if(auth()->user()->is_super)
                                            <button class="btn btn-sm btn-dark w-50 mb-2"
                                                    @click="showReport('Outlet Wise Sales Summary')">
                                                Show Outlet Wise Sales Summary
                                            </button>
                                        @endif
                                        <button class="btn btn-sm btn-dark w-50 mb-2"
                                                @click="showReport('All Customer Sales Details')">
                                            Show All Customer Sales Details
                                        </button>
                                        {{--                                        <button class="btn btn-sm btn-dark w-50 mb-2" @click="showReport('trial_balance')">--}}
                                        {{--                                          Show Customer Wise Sales Details--}}
                                        {{--                                        </button>--}}
                                        <button class="btn btn-sm btn-dark w-50 mb-2"
                                                @click="showReport('Single Item Sales Details')">
                                            Show Single Item Sales Details
                                        </button>
                                        <button class="btn btn-sm btn-dark w-50 mb-2"
                                                @click="showReport('Single Customer Details')">
                                            Show Single Customer Details
                                        </button>
                                        @if(auth()->user()->employee->user_of != 'outlet')
                                        <hr>
                                        {{-- Due and Discount Report --}}
                                        <button class="btn btn-sm btn-dark w-50 mb-2"
                                                @click="showReport('Outlet Wise Due')">
                                            Show Outlet Wise Due
                                        </button>
                                        <button class="btn btn-sm btn-dark w-50 mb-2"
                                                @click="showReport('Single Customer Due')">
                                            Show Single Customer Due
                                        </button>
                                        <button class="btn btn-sm btn-dark w-50 mb-2"
                                                @click="showReport('Outlet Wise Discount')">
                                            Show Outlet Wise Discount
                                        </button>
                                        <button class="btn btn-sm btn-dark w-50 mb-2"
                                                @click="showReport('Single Customer Discount')">
                                            Show Single Customer Discount
                                        </button>
                                        {{-- End Due and Discount Report --}}
                                        @endif
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
                        inventoryReportUrl: "{{ url('sale-reports') }}",
                        get_all_categories_url: "{{ url('pos-categories') }}",
                        get_items_info_by_group_id_url: "{{ url('fetch-items-by-group-id') }}",
                    },
                    from_date: new Date(),
                    to_date: new Date(),
                    as_on_date: new Date(),
                    pageLoading: false,
                    stores: [],
                    groups: [],
                    items: [],
                    store_id: '',
                    group_id: '',
                    item_id: '',
                    customers: [],
                    customer_id: ''

                },
                components: {
                    vuejsDatepicker,
                },
                mounted: function () {
                    this.getStores()
                    this.getGroups()
                    this.getCustomers()
                },
                methods: {
                    showReport(reportType) {
                        const vm = this;
                        if (reportType === 'Single Item Sales Details') {
                            if (!vm.item_id) {
                                toastr.error('Please Select an Item', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            }
                        }
                        if (reportType === 'Outlet Wise Discount' || reportType === 'Outlet Wise Due') {
                            if (!vm.store_id) {
                                toastr.error('Please Select an Outlet', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            }
                        }
                        if (reportType === 'Single Customer Details' || reportType === 'Single Customer Due' || reportType === 'Single Customer Discount') {
                            if (!vm.customer_id) {
                                toastr.error('Please Select a Customer', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            }
                        }

                        vm.pageLoading = true;
                        axios.get(this.config.inventoryReportUrl + '/create', {
                            params: {
                                report_type: reportType,
                                as_on_date: vm.as_on_date,
                                from_date: vm.from_date,
                                to_date: vm.to_date,
                                item_id: vm.item_id,
                                customer_id: vm.customer_id,
                                store_id: vm.store_id,
                            },
                            responseType: 'blob',
                        }).then(function (response) {
                            if (response.data.size === 17) {
                                toastr.info('No Data Found', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                vm.pageLoading = false;
                                return false;
                            }
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
                    },
                    getStores() {
                        const vm = this;
                        axios.get('/get-all-fg-stores').then(function (response) {
                            console.log(response)
                            vm.stores = response.data;
                        }).catch(function (error) {
                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });
                    },
                    getGroups() {
                        const vm = this;
                        axios.get(this.config.get_all_categories_url).then(function (response) {
                            vm.groups = response.data;
                        }).catch(function (error) {
                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });
                    },
                    getCustomers() {
                        const vm = this;
                        axios.get('/get-all-customers').then(function (response) {
                            vm.customers = response.data;
                        }).catch(function (error) {
                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });
                    },
                    getItemsByGroup() {
                        const vm = this;
                        const slug = vm.group_id;
                        if (slug) {
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
                },

                updated() {
                    $('.bSelect').selectpicker('refresh');
                }
            });
            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });
        });
    </script>
@endpush
