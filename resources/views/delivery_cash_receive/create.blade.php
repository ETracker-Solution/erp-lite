@extends('layouts.app')
@section('title', 'Delivery Cash Transfer Entry')
@push('style')

@endpush
@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Delivery Cash Transfer' => route('delivery-cash-transfers.index'),
            'Delivery Cash Transfer Entry' => '',
        ];
    @endphp
    <x-breadcrumb title='Delivery Cash Transfer' :links="$links"/>
    <!-- Basic Inputs start -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{ route('delivery-cash-transfers.store') }}" method="POST" class="" enctype="multipart/form-data">
                                @csrf
                                <div class="card">
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
                                                                <label for="uid">Others Outlet Invoice</label>
                                                                <select class="form-control select2"
                                                                    id="invoice_number">
                                                                    <option value="">---Select One---</option>
                                                                    @foreach ($othersOutlets as $row)
                                                                        <option value="{{ $row }}" {{ old('invoice_number') == $row->id ? 'selected' : '' }}>{{ $row->invoice_number }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <input type="hidden" name="sale_id">
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
                                                                <label for="credit_account_id">Transfer From</label>
                                                                <select class="form-control select2" name="credit_account_id"
                                                                    id="credit_account_id">
                                                                    <option value="">---Select Account---</option>
                                                                    @foreach ($chartOfAccounts as $row)
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
                                <div class="card">
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
                                                                    <label for="debit_account_id">Transfer To</label>
                                                                    <select class="form-control select2" name="debit_account_id"
                                                                        id="debit_account_id">
                                                                        <option value="">---Select Account---</option>
                                                                        @foreach ($toChartOfAccounts as $row)
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
                                            type="submit" onclick="return confirm('Are you sure?');">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script>
        $('#invoice_number').on('select2:select', function (e) {
                const othersoutlet = JSON.parse(e.params.data.id);
                console.log(othersoutlet)
                $('input[name=sale_id]').val(othersoutlet.id)
                $('input[name=amount]').val(othersoutlet.delivery_point_receive_amount)
            })
    </script>
@endpush
