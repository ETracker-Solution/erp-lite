@extends('layouts.app')

@section('title', 'Receive Voucher Entry')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Accounts Module' => '',
            'General Accounts' => '',
            'Receive Voucher' => route('receive-vouchers.index'),
            'Create' => '',
        ];
    @endphp
    <x-breadcrumb title="Receive Voucher" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <form action="{{ route('receive-vouchers.store') }}" method="POST" class="prevent-enter-submit">
                        @csrf
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title mb-0">New Receive Voucher</h3>
                                <div class="card-tools">
                                    <a href="{{ route('receive-vouchers.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list"></i> List
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    Money received into <strong>Cash/Bank (Dr)</strong> from a
                                    <strong>source account (Cr)</strong>.
                                </p>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="uid">RV No <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="uid" name="uid"
                                                   value="{{ old('uid', $RVno) }}" readonly>
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
                                            <label for="amount">Amount <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="amount" name="amount"
                                                   min="0.01" step="0.01" placeholder="0.00"
                                                   value="{{ old('amount') }}" required>
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
                                                Receive Account (Dr)
                                                <small class="text-muted">Cash / Bank</small>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control select2" name="debit_account_id"
                                                    id="debit_account_id" style="width:100%" required>
                                                <option value="">Select receive account</option>
                                                @foreach ($debitAccounts as $row)
                                                    <option value="{{ $row->id }}"
                                                        {{ (string) old('debit_account_id') === (string) $row->id ? 'selected' : '' }}>
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
                                                <small class="text-muted">Source / Income</small>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control select2" name="credit_account_id"
                                                    id="credit_account_id" style="width:100%" required>
                                                <option value="">Select credit account</option>
                                                @foreach ($creditAccounts as $row)
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="payee_name">Received From <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="payee_name" name="payee_name"
                                                   placeholder="Person / party name"
                                                   value="{{ old('payee_name') }}" required>
                                            @error('payee_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
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
                                <a href="{{ route('receive-vouchers.index') }}" class="btn btn-default">Cancel</a>
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
