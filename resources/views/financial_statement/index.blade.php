@extends('layouts.app')
@section('title')
    RM Inventory Report
@endsection
@section('content')
    @php
        $links = [
       'Home'=>route('dashboard'),
       'Accounts Module'=>'',
       'Financial Statement'=>'',
        ]
    @endphp
    <x-breadcrumb title='Financial Statement' :links="$links"/>

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
{{--                                <div class="col-12">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="store_id">Ledger Group</label>--}}
{{--                                        <select name="store_id" id="store_id" class="form-control" v-model="store_id">--}}
{{--                                            <option value="">Select a Group</option>--}}
{{--                                            <option :value="row.id" v-for="row in stores"--}}
{{--                                            >@{{ row.id + ' - ' + row.name }}--}}
{{--                                            </option>--}}
{{--                                        </select>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
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
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">As On Date</label>
                                        <vuejs-datepicker v-model="as_on_date" name="as_on_date"
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
                            <div class="card-title">Financial Statement</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center">
                                        <button class="btn btn-sm btn-dark w-50 mb-2" @click="showReport('income_statement')">
                                           Income Statement
                                        </button>
                                        <button class="btn btn-sm btn-dark w-50 mb-2"
                                                @click="showReport('balance_sheet')">Balance Sheet
                                        </button>
                                        <button class="btn btn-sm btn-dark w-50 mb-2" @click="showReport('trial_balance')">
                                           Trial Balance
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
                        inventoryReportUrl: "{{ url('financial-statements') }}",
                    },
                    from_date: new Date(),
                    to_date: new Date(),
                    as_on_date: new Date(),
                    pageLoading: false,

                },
                components: {
                    vuejsDatepicker,
                },
                computed: {},
                methods: {
                    showReport(reportType) {
                        const vm = this;
                        if (reportType === 'income_statement') {
                            if (!vm.group_id) {
                                toastr.warning('Under Construction', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            }
                        }
                        if (reportType === 'trial_balance') {
                            if (!vm.store_id) {
                                toastr.warning('Under Construction', {
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
                                as_on_date: vm.as_on_date
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
                }
            });
            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });
        });
    </script>
@endpush
