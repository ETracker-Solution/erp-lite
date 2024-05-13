@extends('layouts.app')
@section('title', 'Supplier Voucher Create')
@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Supplier Voucher' => route('supplier-vouchers.index'),
            'Supplier Voucher create' => '',
        ];
    @endphp
    <x-breadcrumb title='Supplier Voucher' :links="$links"/>
    <!-- Basic Inputs start -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{ route('supplier-vouchers.store') }}" method="POST" class="" enctype="multipart/form-data">
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

                                                        <div class="col-xl-12 col-md-12 col-12">
                                                            <div class="form-group">
                                                                <label for="sv_no">SV No</label>
                                                                <input type="number" class="form-control" id="sv_no"
                                                                        name="sv_no" placeholder="Enter RV No"
                                                                        value="{{ old('sv_no') }}" readonly>
                                                                @if ($errors->has('sv_no'))
                                                                    <small class="text-danger">{{ $errors->first('sv_no') }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-12 col-md-12 col-12">
                                                            <div class="form-group">
                                                                <label for="date">Date</label>
                                                                <div class="input-group date" id="reservationdate"
                                                                    data-target-input="nearest">
                                                                    <input type="text" name="date" class="form-control datetimepicker-input"
                                                                        data-target="#reservationdate" />
                                                                    <div class="input-group-append" data-target="#reservationdate"
                                                                        data-toggle="datetimepicker">
                                                                        <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-12 col-md-12 col-12">
                                                            <div class="form-group">
                                                                <label for="supplier_id">Supplier</label>
                                                                <select class="form-control select2" name="supplier_id"
                                                                    id="supplier_id">
                                                                    <option value="">---Select Account---</option>
                                                                    @foreach ($suppliers as $row)
                                                                        <option value="{{ $row->id }}" {{ old('supplier_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

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
                                                    </div>
                                                    <div class="row">

                                                        <div class="col-xl-12 col-md-12 col-12">
                                                            <div class="form-group">
                                                                <label for="credit_account_id">Credit Account</label>
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
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                        </div>
                                                        <div class="col-md-6">
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
                                        <button class="float-right btn btn-primary waves-effect waves-float waves-light"
                                            type="submit">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->
    <input type="hidden" id="supplierVoucaher"  value="{{ $SVno }}"/>

@endsection
@push('script')
    <script>
        var i = parseInt(document.getElementById('supplierVoucaher').value);

        document.getElementById('sv_no').value = i;
    </script>
@endpush
