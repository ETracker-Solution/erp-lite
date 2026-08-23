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
                                        <select name="supplier_id" id="supplier_id" class="form-control" style="width:100%"></select>
                                        <small class="text-muted">Type at least 2 characters to search (keeps page light).</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="customer_id">Customer</label>
                                        <select name="customer_id" id="customer_id" class="form-control" style="width:100%"></select>
                                        <small class="text-muted">Type at least 2 characters to search (keeps page light).</small>
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
    <script src="{{ asset('vue-js/vuejs-datepicker.js') }}"></script>
    <script>
        $(document).ready(function () {
            function initPartySelect($el, url, placeholder) {
                $el.select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: placeholder,
                    allowClear: true,
                    minimumInputLength: 2,
                    ajax: {
                        url: url,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return { q: params.term || '' };
                        },
                        processResults: function (data) {
                            return {
                                results: (data.results || []).map(function (row) {
                                    return {
                                        id: row.id,
                                        text: row.id + ' - ' + row.name + (row.mobile ? ' (' + row.mobile + ')' : '')
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });
            }

            initPartySelect($('#supplier_id'), "{{ url('ledger-reports-search-suppliers') }}", 'Search supplier…');
            initPartySelect($('#customer_id'), "{{ url('ledger-reports-search-customers') }}", 'Search customer…');

            var vue = new Vue({
                el: '#vue_app',
                data: {
                    config: {
                        ledger_report_url: "{{ url('ledger-reports') }}",
                        initial_info_url: "{{ url('ledger-reports-initial-info') }}",
                    },
                    from_date: new Date(),
                    to_date: new Date(),
                    account_id: '',
                    supplier_id: '',
                    customer_id: '',
                    accounts: [],
                    pageLoading: false,
                },
                components: {
                    vuejsDatepicker,
                },
                methods: {
                    get_initial_data() {
                        const vm = this;
                        vm.pageLoading = true;
                        axios.get(this.config.initial_info_url).then(function (response) {
                            vm.accounts = response.data.accounts || [];
                            vm.pageLoading = false;
                            vm.$nextTick(function () {
                                $('.bSelect').selectpicker('refresh');
                            });
                        }).catch(function () {
                            vm.pageLoading = false;
                            toastr.error('Failed to load ledger accounts', {
                                closeButton: true,
                                progressBar: true,
                            });
                        });
                    },
                    async readBlobError(error) {
                        try {
                            if (error.response && error.response.data) {
                                const text = await error.response.data.text();
                                const json = JSON.parse(text);
                                if (json.message) return json.message;
                            }
                        } catch (e) {}
                        return 'Something went wrong generating the report';
                    },
                    showReport(reportType) {
                        const vm = this;
                        vm.supplier_id = $('#supplier_id').val() || '';
                        vm.customer_id = $('#customer_id').val() || '';

                        if (reportType === 'account_ledger' && !vm.account_id) {
                            toastr.error('Please Select Ledger A/C', { closeButton: true, progressBar: true });
                            return false;
                        }
                        if (reportType === 'supplier_ledger' && !vm.supplier_id) {
                            toastr.error('Please Select Supplier', { closeButton: true, progressBar: true });
                            return false;
                        }
                        if (reportType === 'customer_ledger' && !vm.customer_id) {
                            toastr.error('Please Select Customer', { closeButton: true, progressBar: true });
                            return false;
                        }

                        function toYmd(value) {
                            if (!value) return '';
                            const d = value instanceof Date ? value : new Date(value);
                            if (isNaN(d.getTime())) return '';
                            const y = d.getFullYear();
                            const m = String(d.getMonth() + 1).padStart(2, '0');
                            const day = String(d.getDate()).padStart(2, '0');
                            return y + '-' + m + '-' + day;
                        }

                        vm.pageLoading = true;
                        axios.get(this.config.ledger_report_url + '/create', {
                            params: {
                                report_type: reportType,
                                from_date: toYmd(vm.from_date),
                                to_date: toYmd(vm.to_date),
                                account_id: vm.account_id,
                                supplier_id: vm.supplier_id,
                                customer_id: vm.customer_id,
                            },
                            responseType: 'blob',
                        }).then(async function (response) {
                            const contentType = (response.headers['content-type'] || '');
                            if (contentType.indexOf('application/json') !== -1) {
                                const text = await response.data.text();
                                let message = 'Unable to generate report';
                                try { message = JSON.parse(text).message || message; } catch (e) {}
                                toastr.error(message, { closeButton: true, progressBar: true });
                                vm.pageLoading = false;
                                return;
                            }
                            const blob = new Blob([response.data], { type: 'application/pdf' });
                            const url = window.URL.createObjectURL(blob);
                            window.open(url);
                            vm.pageLoading = false;
                        }).catch(async function (error) {
                            vm.pageLoading = false;
                            const message = await vm.readBlobError(error);
                            toastr.error(message, { closeButton: true, progressBar: true });
                        });
                    }
                },
                updated() {
                    $('.bSelect').selectpicker('refresh');
                },
                mounted() {
                    this.get_initial_data();
                }
            });

            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });
        });
    </script>
@endpush
