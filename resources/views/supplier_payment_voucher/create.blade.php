@extends('layouts.app')

@section('title', 'Supplier Payment Voucher Entry')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Accounts Module' => '',
            'General Accounts' => '',
            'Supplier Payment Voucher' => route('supplier-vouchers.index'),
            'Create' => '',
        ];
    @endphp
    <x-breadcrumb title="Supplier Payment Voucher" :links="$links"/>

    <section class="content">
        <div class="container-fluid" id="spv_create_app">
            <span v-show="pageLoading" class="pageLoader">
                <img src="{{ asset('loading.gif') }}" alt="loading">
            </span>
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <form action="{{ route('supplier-vouchers.store') }}" method="POST" class="prevent-enter-submit">
                        @csrf
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title mb-0">New Supplier Payment Voucher</h3>
                                <div class="card-tools">
                                    <a href="{{ route('supplier-vouchers.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list"></i> List
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    Supplier payment: <strong>Accounts Payable (Dr)</strong> and
                                    <strong>Cash/Bank payment account (Cr)</strong>.
                                </p>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="uid">SPV No <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="uid" name="uid"
                                                   v-model="uid" readonly>
                                            @error('uid')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="date">Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="date" name="date"
                                                   value="{{ old('date', date('Y-m-d')) }}" required>
                                            @error('date')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="due">Due Amount</label>
                                            <input type="number" class="form-control" id="due"
                                                   v-model="due" readonly placeholder="0.00">
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="supplier_group_id">
                                                Supplier Group <span class="text-danger">*</span>
                                            </label>
                                            <select id="supplier_group_id" class="form-control"
                                                    v-model="supplier_group_id"
                                                    @change="fetchSupplier" required>
                                                <option value="">Select group</option>
                                                @foreach ($supplier_groups as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="supplier_id">
                                                Supplier <span class="text-danger">*</span>
                                            </label>
                                            <select name="supplier_id" id="supplier_id" class="form-control"
                                                    v-model="supplier_id" @change="fetchDue" required>
                                                <option value="">Select supplier</option>
                                                <option v-for="row in suppliers" :key="row.id"
                                                        :value="row.id" v-text="row.name"></option>
                                            </select>
                                            @error('supplier_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="credit_account_id">
                                                Payment Account (Cr)
                                                <small class="text-muted">Cash / Bank</small>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control select2" name="credit_account_id"
                                                    id="credit_account_id" style="width:100%" required>
                                                <option value="">Select payment account</option>
                                                @foreach ($paymentAccounts as $row)
                                                    <option value="{{ $row->id }}"
                                                        {{ (string) old('credit_account_id') === (string) $row->id ? 'selected' : '' }}>
                                                        {{ $row->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('credit_account_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="amount">Payment Amount <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="amount" name="amount"
                                                   min="0.01" step="0.01" placeholder="0.00"
                                                   v-model="amount" @change="validAmount" required>
                                            @error('amount')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="payee_name">Paid To <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="payee_name" name="payee_name"
                                                   placeholder="Person / party name"
                                                   value="{{ old('payee_name') }}" required>
                                            @error('payee_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="reference_no">Reference No</label>
                                            <input type="text" class="form-control" id="reference_no" name="reference_no"
                                                   placeholder="Optional"
                                                   value="{{ old('reference_no') }}">
                                            @error('reference_no')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <label for="narration">Narration</label>
                                    <textarea class="form-control" name="narration" id="narration" rows="3"
                                              placeholder="Optional description">{{ old('narration') }}</textarea>
                                    @error('narration')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer text-right">
                                <a href="{{ route('supplier-vouchers.index') }}" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-info">
                                    <i class="fa fa-save"></i> Save Voucher
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .pageLoader {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 999;
        }
    </style>
@endpush

@push('js_scripts')
    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script>
        $(function () {
            if ($.fn.select2) {
                $('#credit_account_id').select2({
                    width: '100%',
                    placeholder: 'Select payment account',
                    allowClear: true
                });
            }

            new Vue({
                el: '#spv_create_app',
                data: {
                    config: {
                        getSuppliersUrl: "{{ url('fetch-suppliers-by-group-id') }}",
                        getDueUrl: "{{ url('fetch-due-by-supplier-id') }}",
                    },
                    uid: @json((string) old('uid', $uid)),
                    supplier_group_id: '',
                    supplier_id: '',
                    amount: @json(old('amount', '')),
                    due: '',
                    suppliers: [],
                    pageLoading: false
                },
                methods: {
                    fetchSupplier: function () {
                        var vm = this;
                        if (!vm.supplier_group_id) {
                            return;
                        }
                        vm.pageLoading = true;
                        vm.supplier_id = '';
                        vm.due = '';
                        axios.get(vm.config.getSuppliersUrl + '/' + vm.supplier_group_id)
                            .then(function (response) {
                                vm.suppliers = response.data.suppliers || [];
                                vm.pageLoading = false;
                            })
                            .catch(function () {
                                vm.pageLoading = false;
                                toastr.error('Something went wrong', {
                                    closeButton: true,
                                    progressBar: true
                                });
                            });
                    },
                    fetchDue: function () {
                        var vm = this;
                        if (!vm.supplier_id) {
                            return;
                        }
                        vm.pageLoading = true;
                        axios.get(vm.config.getDueUrl + '/' + vm.supplier_id)
                            .then(function (response) {
                                vm.due = response.data;
                                vm.pageLoading = false;
                            })
                            .catch(function () {
                                vm.pageLoading = false;
                                toastr.error('Something went wrong', {
                                    closeButton: true,
                                    progressBar: true
                                });
                            });
                    },
                    validAmount: function () {
                        var vm = this;
                        var amount = parseFloat(vm.amount);
                        var due = parseFloat(vm.due);
                        if (!(amount > 0)) {
                            toastr.error('Amount 0 or negative is not allowed', {
                                closeButton: true,
                                progressBar: true
                            });
                            vm.amount = '';
                            return;
                        }
                        if (!isNaN(due) && amount > due) {
                            toastr.warning('Given amount greater than due amount', {
                                closeButton: true,
                                progressBar: true
                            });
                            vm.amount = due;
                        }
                    }
                }
            });
        });
    </script>
@endpush
