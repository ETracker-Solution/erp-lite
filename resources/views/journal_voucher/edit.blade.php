@extends('layouts.app')

@section('title', 'Journal Voucher Edit')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Accounts Module' => '',
            'General Accounts' => '',
            'Journal Voucher' => route('journal-vouchers.index'),
            'Edit' => '',
        ];
        $voucherDate = $journalVoucher->date;
        if ($voucherDate instanceof \Carbon\CarbonInterface) {
            $voucherDate = $voucherDate->format('Y-m-d');
        }
    @endphp
    <x-breadcrumb title="Journal Voucher" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <form action="{{ route('journal-vouchers.update', $journalVoucher->id) }}"
                          method="POST" class="prevent-enter-submit">
                        @csrf
                        @method('PUT')
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title mb-0">
                                    Edit Journal Voucher
                                    <span class="font-weight-normal ml-1">{{ $journalVoucher->uid }}</span>
                                </h3>
                                <div class="card-tools">
                                    <a href="{{ route('journal-vouchers.show', encrypt($journalVoucher->id)) }}"
                                       class="btn btn-sm btn-secondary">
                                        <i class="fa fa-eye"></i> Show
                                    </a>
                                    <a href="{{ route('journal-vouchers.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list"></i> List
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    Double-entry transfer between two ledger accounts
                                    (<strong>Debit (Dr)</strong> and <strong>Credit (Cr)</strong>).
                                </p>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="uid">JV No</label>
                                            <input type="text" class="form-control" id="uid"
                                                   value="{{ $journalVoucher->uid }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="date">Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="date" name="date"
                                                   value="{{ old('date', $voucherDate) }}" required>
                                            @error('date')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="amount">Amount <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="amount" name="amount"
                                                   min="0.01" step="0.01" placeholder="0.00"
                                                   value="{{ old('amount', $journalVoucher->amount) }}" required>
                                            @error('amount')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="debit_account_id">
                                                Debit Account (Dr)
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control select2" name="debit_account_id"
                                                    id="debit_account_id" style="width:100%" required>
                                                <option value="">Select debit account</option>
                                                @foreach ($chartOfAccounts as $row)
                                                    <option value="{{ $row->id }}"
                                                        {{ (string) old('debit_account_id', $journalVoucher->debit_account_id) === (string) $row->id ? 'selected' : '' }}>
                                                        {{ $row->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('debit_account_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="credit_account_id">
                                                Credit Account (Cr)
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control select2" name="credit_account_id"
                                                    id="credit_account_id" style="width:100%" required>
                                                <option value="">Select credit account</option>
                                                @foreach ($chartOfAccounts as $row)
                                                    <option value="{{ $row->id }}"
                                                        {{ (string) old('credit_account_id', $journalVoucher->credit_account_id) === (string) $row->id ? 'selected' : '' }}>
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="reference_no">Reference No</label>
                                            <input type="text" class="form-control" id="reference_no" name="reference_no"
                                                   placeholder="Optional"
                                                   value="{{ old('reference_no', $journalVoucher->reference_no) }}">
                                            @error('reference_no')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <label for="narration">Narration</label>
                                    <textarea class="form-control" name="narration" id="narration" rows="3"
                                              placeholder="Optional description">{{ old('narration', $journalVoucher->narration) }}</textarea>
                                    @error('narration')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer text-right">
                                <a href="{{ route('journal-vouchers.index') }}" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-info">
                                    <i class="fa fa-save"></i> Update Voucher
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js_scripts')
    <script>
        $(function () {
            if ($.fn.select2) {
                $('#debit_account_id, #credit_account_id').select2({
                    width: '100%',
                    placeholder: 'Select account',
                    allowClear: true
                });
            }
        });
    </script>
@endpush
