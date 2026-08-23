@extends('layouts.app')
@section('title', 'Fund Transfer Voucher Entry')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Accounts Module' => '',
            'General Accounts' => '',
            'Fund Transfer Voucher' => route('fund-transfer-vouchers.index'),
            'Create' => '',
        ];
    @endphp
    <x-breadcrumb title="Fund Transfer Voucher" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="ftv_create_app">
                <span v-show="pageLoading" class="ftv-loader">
                    <img src="{{ asset('loading.gif') }}" alt="loading">
                </span>

                <div class="col-12">
                    <form action="{{ route('fund-transfer-vouchers.store') }}"
                          method="POST"
                          class="prevent-enter-submit"
                          @submit="onSubmit">
                        @csrf
                        <input type="hidden" name="submission_token"
                               value="{{ session()->get('submission_token') ?? Str::random(40) }}">

                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title mb-0">FT Voucher Entry</h3>
                                <div class="card-tools">
                                    <a href="{{ route('fund-transfer-vouchers.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-list"></i> See List
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                @if(!empty($officeAccountsMissing))
                                    <div class="alert alert-warning mb-3">
                                        <strong>No Office Account found.</strong>
                                        “Transfer To” needs a ledger tagged as
                                        <code>office_account</code> with Bank/Cash = Yes.
                                        Open Chart of Accounts → set
                                        <em>Default Type = Office Account</em>, then reload.
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="ftv_date">Date <span class="text-danger">*</span></label>
                                            <input type="date" id="ftv_date" name="date" class="form-control"
                                                   v-model="date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label for="narration">Remark</label>
                                            <input type="text" id="narration" name="narration" class="form-control"
                                                   placeholder="Optional narration" maxlength="500">
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                <div class="row align-items-end">
                                    <div class="col-md-3">
                                        <div class="form-group mb-2">
                                            <label for="from_account_id">
                                                Transfer From
                                                <small class="text-muted" v-if="from_ac_balance !== ''">
                                                    (Avail: @{{ formatMoney(from_ac_balance) }})
                                                </small>
                                            </label>
                                            <select id="from_account_id" class="form-control select2" style="width:100%">
                                                <option value="">Select account</option>
                                                @foreach($fromAccounts as $account)
                                                    <option value="{{ $account['id'] }}">{{ $account['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @if(count($fromAccounts) === 0)
                                                <small class="text-danger">No transferable from-accounts found.</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-2">
                                            <label for="to_account_id">Transfer To</label>
                                            <select id="to_account_id" class="form-control select2" style="width:100%">
                                                <option value="">Select account</option>
                                                @foreach($toAccounts as $account)
                                                    <option value="{{ $account['id'] }}">{{ $account['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @if(count($toAccounts) === 0)
                                                <small class="text-danger">No transfer-to accounts found.</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mb-2">
                                            <label for="reference_no">Reference No</label>
                                            <input type="text" id="reference_no" class="form-control"
                                                   v-model.trim="reference_no"
                                                   @keyup.enter.prevent="addLine"
                                                   placeholder="Optional">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mb-2">
                                            <label for="amount">Amount <span class="text-danger">*</span></label>
                                            <input type="number" id="amount" class="form-control" min="0.01" step="0.01"
                                                   v-model.number="amount"
                                                   @keyup.enter.prevent="addLine"
                                                   placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mb-2">
                                            <button type="button" class="btn btn-info btn-block" @click="addLine"
                                                    :disabled="pageLoading">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive mt-2">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead class="thead-light">
                                        <tr>
                                            <th style="width:5%">#</th>
                                            <th>Transfer From</th>
                                            <th>Transfer To</th>
                                            <th>Reference</th>
                                            <th class="text-right" style="width:18%">Amount</th>
                                            <th style="width:5%"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="selected_items.length === 0">
                                            <td colspan="6" class="text-center text-muted py-3">
                                                No lines added yet
                                            </td>
                                        </tr>
                                        <tr v-for="(row, index) in selected_items" :key="row._key">
                                            <td>@{{ index + 1 }}</td>
                                            <td>
                                                @{{ row.from_account_name }}
                                                <input type="hidden"
                                                       :name="'products[' + index + '][credit_account_id]'"
                                                       :value="row.from_account_id">
                                            </td>
                                            <td>
                                                @{{ row.to_account_name }}
                                                <input type="hidden"
                                                       :name="'products[' + index + '][debit_account_id]'"
                                                       :value="row.to_account_id">
                                            </td>
                                            <td>
                                                @{{ row.reference_no || '—' }}
                                                <input type="hidden"
                                                       :name="'products[' + index + '][reference_no]'"
                                                       :value="row.reference_no">
                                            </td>
                                            <td class="text-right font-weight-bold">
                                                @{{ formatMoney(row.amount) }}
                                                <input type="hidden"
                                                       :name="'products[' + index + '][amount]'"
                                                       :value="row.amount">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-xs btn-danger"
                                                        @click="removeLine(index)" title="Remove">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot v-if="selected_items.length > 0">
                                        <tr>
                                            <td colspan="4" class="text-right font-weight-bold">Subtotal</td>
                                            <td class="text-right font-weight-bold">
                                                @{{ formatMoney(subtotal) }}
                                                <input type="hidden" name="subtotal" :value="subtotal">
                                            </td>
                                            <td></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="card-footer text-right" v-show="selected_items.length > 0">
                                <button class="btn btn-primary" type="submit" :disabled="submitting">
                                    <i class="fa fa-check-circle"></i>
                                    <span v-if="!submitting">Submit</span>
                                    <span v-else>Submitting...</span>
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
        .ftv-loader {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 20;
        }
        #ftv_create_app { position: relative; min-height: 200px; }
    </style>
@endpush

@push('script')
    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script>
        $(function () {
            const balanceUrl = @json(url('fetch-from-account-balance'));

            function bindSelect2(vm) {
                const $from = $('#from_account_id');
                const $to = $('#to_account_id');

                if (!$.fn.select2) {
                    setTimeout(function () { bindSelect2(vm); }, 50);
                    return;
                }

                // Re-init safely (global layout may also init .select2)
                if ($from.hasClass('select2-hidden-accessible')) {
                    $from.select2('destroy');
                }
                if ($to.hasClass('select2-hidden-accessible')) {
                    $to.select2('destroy');
                }

                $from.select2({ width: '100%', placeholder: 'Select account', allowClear: true });
                $to.select2({ width: '100%', placeholder: 'Select account', allowClear: true });

                $from.off('change.ftv').on('change.ftv', function () {
                    vm.from_account_id = this.value || '';
                    vm.fetchFromBalance();
                });
                $to.off('change.ftv').on('change.ftv', function () {
                    vm.to_account_id = this.value || '';
                });
            }

            new Vue({
                el: '#ftv_create_app',
                data: {
                    date: @json(now()->toDateString()),
                    from_account_id: '',
                    to_account_id: '',
                    reference_no: '',
                    amount: '',
                    from_ac_balance: '',
                    selected_items: [],
                    pageLoading: false,
                    submitting: false,
                    lineKey: 0,
                },
                computed: {
                    subtotal() {
                        return this.selected_items.reduce((sum, row) => sum + (parseFloat(row.amount) || 0), 0);
                    },
                },
                mounted() {
                    bindSelect2(this);
                },
                methods: {
                    formatMoney(value) {
                        const n = parseFloat(value);
                        if (isNaN(n)) return '0.00';
                        return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    },
                    fetchFromBalance() {
                        const vm = this;
                        if (!vm.from_account_id) {
                            vm.from_ac_balance = '';
                            return;
                        }
                        vm.pageLoading = true;
                        axios.get(balanceUrl + '/' + vm.from_account_id)
                            .then(function (response) {
                                vm.from_ac_balance = response.data.from_ac_balance;
                            })
                            .catch(function () {
                                vm.from_ac_balance = '';
                                toastr.error('Unable to fetch account balance');
                            })
                            .finally(function () {
                                vm.pageLoading = false;
                            });
                    },
                    resetLineInputs() {
                        this.from_account_id = '';
                        this.to_account_id = '';
                        this.reference_no = '';
                        this.amount = '';
                        this.from_ac_balance = '';
                        $('#from_account_id').val('').trigger('change');
                        $('#to_account_id').val('').trigger('change');
                    },
                    addLine() {
                        const fromId = String(this.from_account_id || $('#from_account_id').val() || '');
                        const toId = String(this.to_account_id || $('#to_account_id').val() || '');
                        const amount = parseFloat(this.amount);

                        if (!fromId || !toId) {
                            toastr.error('Please select Transfer From and Transfer To');
                            return;
                        }
                        if (fromId === toId) {
                            toastr.error('Transfer From and Transfer To must be different');
                            return;
                        }
                        if (!amount || amount <= 0) {
                            toastr.error('Please enter a valid amount');
                            return;
                        }
                        if (this.from_ac_balance !== '' && amount > parseFloat(this.from_ac_balance)) {
                            toastr.error('Amount exceeds available balance');
                            return;
                        }
                        if (this.selected_items.some(row => String(row.from_account_id) === fromId)) {
                            toastr.info('This Transfer From account is already added');
                            return;
                        }

                        const fromName = $('#from_account_id option:selected').text().trim();
                        const toName = $('#to_account_id option:selected').text().trim();

                        this.selected_items.push({
                            _key: ++this.lineKey,
                            from_account_id: fromId,
                            from_account_name: fromName,
                            to_account_id: toId,
                            to_account_name: toName,
                            reference_no: this.reference_no || '',
                            amount: amount,
                        });

                        this.resetLineInputs();
                    },
                    removeLine(index) {
                        this.selected_items.splice(index, 1);
                    },
                    onSubmit(e) {
                        if (this.selected_items.length < 1) {
                            e.preventDefault();
                            toastr.error('Please add at least one transfer line');
                            return;
                        }
                        if (this.submitting) {
                            e.preventDefault();
                            return;
                        }
                        this.submitting = true;
                    },
                },
            });
        });
    </script>
@endpush
