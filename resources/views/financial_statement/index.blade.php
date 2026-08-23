@extends('layouts.app')
@section('title')
    Financial Statement
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
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">From Date <small class="text-muted">(Income Statement / Cash Flow)</small></label>
                                        <vuejs-datepicker v-model="from_date" name="from_date"
                                                          placeholder="Select date"></vuejs-datepicker>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">To Date <small class="text-muted">(Income Statement / Cash Flow)</small></label>
                                        <vuejs-datepicker v-model="to_date" name="to_date"
                                                          placeholder="Select date"></vuejs-datepicker>
                                    </div>
                                </div>
                                <hr>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">As On Date <small class="text-muted">(Balance Sheet / Trial Balance)</small></label>
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
                                        <button class="btn btn-sm btn-dark w-50 mb-2" @click="showReport('cash_flow')">
                                           Cash Flow
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
@endpush
@push('script')
    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script src="{{ asset('vue-js/vuejs-datepicker.js') }}"></script>
    <script>
        $(document).ready(function () {
            function toYmd(value) {
                if (!value) return '';
                const d = value instanceof Date ? value : new Date(value);
                if (isNaN(d.getTime())) return '';
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return y + '-' + m + '-' + day;
            }

            new Vue({
                el: '#vue_app',
                data: {
                    config: {
                        reportUrl: "{{ url('financial-statements') }}",
                    },
                    from_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1),
                    to_date: new Date(),
                    as_on_date: new Date(),
                    pageLoading: false,
                },
                components: {
                    vuejsDatepicker,
                },
                methods: {
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
                        vm.pageLoading = true;
                        axios.get(this.config.reportUrl + '/create', {
                            params: {
                                report_type: reportType,
                                from_date: toYmd(vm.from_date),
                                to_date: toYmd(vm.to_date),
                                as_on_date: toYmd(vm.as_on_date),
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
                            window.open(window.URL.createObjectURL(blob));
                            vm.pageLoading = false;
                        }).catch(async function (error) {
                            vm.pageLoading = false;
                            toastr.error(await vm.readBlobError(error), {
                                closeButton: true,
                                progressBar: true,
                            });
                        });
                    }
                },
            });
        });
    </script>
@endpush
