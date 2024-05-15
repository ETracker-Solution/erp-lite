@extends('layouts.app')
@section('title', 'Payment Voucher Create')
@push('style')

@endpush
@section('content')
@php
$links = [
    'Home' => route('dashboard'),
    'Payment Voucher' => route('payment-vouchers.index'),
    'Payment Voucher create' => '',
];
@endphp
<x-breadcrumb title='Payment Voucher' :links="$links"/>

<!-- Basic Inputs start -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('payment-vouchers.store') }}" method="POST" class="" enctype="multipart/form-data">
                            @csrf
                            <div class="card card-info">
                                <div class="card-header">
                                    <h4 class="card-title">Account Type</h4>
                                </div>
                                <hr style="margin: 0;">
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-xl-4 col-md-4 col-12">
                                                        <div class="form-group">
                                                            <label for="uid">PV No</label>
                                                            <input type="number" class="form-control" id="uid"
                                                                   name="uid" placeholder="Enter Amount"
                                                                   value="{{ old('uid') }}" readonly>
                                                            @if ($errors->has('uid'))
                                                                <small class="text-danger">{{ $errors->first('uid') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4 col-12">
                                                        <div class="form-group">
                                                            <label for="date">Date</label>
                                                            <div class="input-group date" id="reservationdate"
                                                                data-target-input="nearest">
                                                                <input type="text" name="date" value="{{ date('Y-m-d') }}" class="form-control datetimepicker-input"
                                                                    data-target="#reservationdate" />
                                                                <div class="input-group-append" data-target="#reservationdate"
                                                                    data-toggle="datetimepicker">
                                                                    <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4 col-12">
                                                        <div class="form-group">
                                                            <label for="cash_bank_account_id">Payment Account</label>
                                                            <select class="form-control select2" name="credit_account_id"
                                                                id="credit_account_id">
                                                                <option value="">---Select Account---</option>
                                                                @foreach ($creditAccounts as $row)
                                                                    <option value="{{ $row->id }}" {{ old('credit_account_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-info">
                                <div class="card-header">
                                    <h4 class="card-title">Account Type</h4>
                                </div>
                                <hr style="margin: 0;">
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="col-xl-12 col-md-12 col-12">
                                                            <div class="form-group">
                                                                <label for="debit_account_id">Debit Account</label>
                                                                <select class="form-control select2" name="debit_account_id"
                                                                    id="debit_account_id">
                                                                    <option value="">---Select Account---</option>
                                                                    @foreach ($debitAccounts as $row)
                                                                        <option value="{{ $row->id }}" {{ old('debit_account_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-12 col-md-12 col-12">
                                                            <div class="form-group">
                                                                <label for="amount">Amount</label>
                                                                <input type="number" class="form-control" id="amount"
                                                                    name="amount" placeholder="Enter Amount"
                                                                    value="{{ old('amount') }}">
                                                                @if ($errors->has('amount'))
                                                                    <small class="text-danger">{{ $errors->first('amount') }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-12 col-md-12 col-12">
                                                            <div class="form-group">
                                                                <label for="payee_name">Reciever Name</label>
                                                                <input type="text" class="form-control" id="payee_name"
                                                                    name="payee_name" placeholder="Enter Reciever Name"
                                                                    value="{{ old('payee_name') }}">
                                                                @if ($errors->has('payee_name'))
                                                                    <small class="text-danger">{{ $errors->first('payee_name') }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-12 col-md-12 col-12">
                                                            <div class="form-group">
                                                                <label for="reference_no">Referance No</label>
                                                                <input type="text" class="form-control" id="reference_no"
                                                                    name="reference_no" placeholder="Enter Referance No"
                                                                    value="{{ old('reference_no') }}">
                                                                @if ($errors->has('reference_no'))
                                                                    <small class="text-danger">{{ $errors->first('reference_no') }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-12 col-md-12 col-12">
                                                            <div class="form-group">
                                                                <label for="narration">Description</label>
                                                                <textarea class="form-control" name="narration" id="narration" cols="" rows="3" placeholder="Enter Description">{{ old('narration') }}</textarea>
                                                                @if ($errors->has('narration'))
                                                                    <small class="text-danger">{{ $errors->first('narration') }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button class="float-right btn btn-info waves-effect waves-float waves-light"
                                        type="submit">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="paymentVoucaher"  value="{{ $PVno }}"/>
</section>
<!-- Basic Inputs end -->

@endsection
@push('script')
    <script>
        var i = parseInt(document.getElementById('paymentVoucaher').value);
    
        document.getElementById('uid').value = i;
    </script>
@endpush
