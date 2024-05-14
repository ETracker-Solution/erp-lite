@extends('layouts.app')
@section('title')
    Ledger Report
@endsection
@section('content')
    @php
        $links = [
       'Home'=>route('dashboard'),
       'Accounts Module'=>'',
       'Ledger Reports'=>'',
        ]
    @endphp
    <x-breadcrumb title='Ledger Reports' :links="$links"/>

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
                                        <label for="account_id">Ledger A/C</label>
                                        <select name="account_id" id="account_id" class="form-control bSelect" v-model="account_id">
                                            <option value="">Select a Ledger A/C</option>
                                            <option :value="row.id" v-for="row in accounts"
                                            >@{{ row.id + ' - ' + row.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="supplier_id">Supplier</label>
                                        <select name="supplier_id" id="supplier_id" class="form-control bSelect" v-model="supplier_id">
                                            <option value="">Select a Supplier</option>
                                            <option :value="row.id" v-for="row in suppliers"
                                            >@{{ row.id + ' - ' + row.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="customer_id">Customer</label>
                                        <select name="customer_id" id="customer_id" class="form-control bSelect" v-model="customer_id">
                                            <option value="">Select a Customer</option>
                                            <option :value="row.id" v-for="row in customers"
                                            >@{{ row.id + ' - ' + row.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">Select Date Range</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">From Date</label>
                                        <vuejs-datepicker v-model="from_date" name="from_date"
                                                          placeholder="Select date"></vuejs-datepicker>
                                    </div>
                                </div>
                                <div class="col-6">
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
                            <div class="card-title">Ledger Report</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center">
                                        <button class="btn btn-sm btn-dark w-50 mb-2" @click="showReport('account_ledger')">
                                            Show General Account Ledger
                                        </button>
                                        <button class="btn btn-sm btn-dark w-50 mb-2"
                                                @click="showReport('supplier_ledger')">Show Supplier Account Ledger
                                        </button>
                                        <button class="btn btn-sm btn-dark w-50 mb-2" @click="showReport('customer_ledger')">
                                            Show Customer Account Ledger
                                        </button>
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
                        ledger_report_url: "{{ url('ledger-reports') }}",
                        initial_info_url: "{{ url('ledger-reports-initial-info') }}",
                    },
                    from_date: new Date(),
                    to_date: new Date(),
                    group_id: '',
                    account_id: '',
                    supplier_id: '',
                    customer_id: '',

                    accounts: [],
                    suppliers: [],
                    customers: [],
                    pageLoading: false,

                    store_id: '',
                    stores: []

                },
                components: {
                    vuejsDatepicker,
                },
                computed: {},
                methods: {

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
                            vm.accounts = response.data.accounts;
                            vm.suppliers= response.data.suppliers;
                            vm.customers= response.data.customers;
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
                        if (reportType === 'account_ledger') {
                            if (!vm.account_id) {
                                toastr.error('Please Select Ledger A/C', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            }
                        }
                        if (reportType === 'supplier_ledger') {
                            if (!vm.supplier_id) {
                                toastr.error('Please Select Supplier', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            }
                        }
                        if (reportType === 'customer_ledger') {
                            if (!vm.customer_id) {
                                toastr.error('Please Select Customer', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            }
                        }

                        vm.pageLoading = true;
                        axios.get(this.config.ledger_report_url + '/create', {
                            params: {
                                report_type: reportType,
                                from_date: vm.from_date,
                                to_date: vm.to_date,
                                account_id: vm.account_id,
                                supplier_id: vm.supplier_id,
                                customer_id: vm.customer_id,
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
