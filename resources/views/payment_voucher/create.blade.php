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
                                                    <div class="col-xl-12 col-md-12 col-12">
                                                        <div class="form-group">
                                                            <label for="pv_no">PV No</label>
                                                            <input type="number" class="form-control" id="pv_no"
                                                                   name="pv_no" placeholder="Enter Amount"
                                                                   value="{{ old('pv_no') }}" readonly>
                                                            @if ($errors->has('pv_no'))
                                                                <small class="text-danger">{{ $errors->first('pv_no') }}</small>
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
    <input type="hidden" id="paymentVoucaher"  value="{{ $PVno }}"/>
</section>
<!-- Basic Inputs end -->

@endsection
@push('script')
<script src="{{asset('admin/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('admin/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{asset('admin/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
<script src="{{asset('admin/app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
<script src="{{asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
    <script>
        $.fn.extend({
            treed: function(o) {

                var openedClass = 'fas fa-minus';
                var closedClass = 'fas fa-plus';

                if (typeof o != 'undefined') {
                    if (typeof o.openedClass != 'undefined') {
                        openedClass = o.openedClass;
                    }
                    if (typeof o.closedClass != 'undefined') {
                        closedClass = o.closedClass;
                    }
                };

                //initialize each of the top levels
                var tree = $(this);
                tree.addClass("tree");
                tree.find('li').has("ul").each(function() {
                    var branch = $(this); //li with children ul
                    branch.prepend("<i class='indicator fas " + closedClass + "'></i>");
                    branch.addClass('branch');
                    branch.on('click', function(e) {
                        if (this == e.target) {
                            var icon = $(this).children('i:first');
                            icon.toggleClass(openedClass + " " + closedClass);
                            $(this).children().children().toggle();
                        }
                    })
                    branch.children().children().toggle();
                });
                //fire event from the dynamically added icon
                tree.find('.branch .indicator').each(function() {
                    $(this).on('click', function() {
                        $(this).closest('li').click();
                    });
                });
                //fire event to open branch if the li contains an anchor instead of text
                tree.find('.branch>a').each(function() {
                    $(this).on('click', function(e) {
                        $(this).closest('li').click();
                        e.preventDefault();
                    });
                });
                //fire event to open branch if the li contains a button instead of text
                tree.find('.branch>button').each(function() {
                    $(this).on('click', function(e) {
                        $(this).closest('li').click();
                        e.preventDefault();
                    });
                });
            }
        });

        //Initialization of treeviews

        $('#tree1').treed();

        var click = 0
        function changeChart(parent_id){
            $('select[name="parent_id"]').val($(event.target)[0].id).change().attr('selected', 'selected')
            event.preventDefault
        }
    </script>
    <script>
        var i = parseInt(document.getElementById('paymentVoucaher').value);
    
        document.getElementById('pv_no').value = i;
    </script>
@endpush
