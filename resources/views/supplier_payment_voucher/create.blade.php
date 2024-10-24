@extends('layouts.app')
@section('title', 'Supplier Payment Voucher Entry')
@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Accounts Module'=>'',
            'General Accounts'=>'',
            'Supplier Payment Voucher Entry' => '',
        ];
    @endphp
    <x-breadcrumb title='Supplier Payment Voucher' :links="$links"/>
    <!-- Basic Inputs start -->
    <section class="content">
        <div class="container-fluid" id="vue_app">
            <span v-if="pageLoading" class="pageLoader">
                <img src="{{ asset('loading.gif') }}" alt="loading">
            </span>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Supplier Payment</h4>
                            <div class="card-tools">
                                <a href="{{route('supplier-vouchers.index')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i>
                                        &nbsp;See List
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('supplier-vouchers.store') }}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        {{-- <div class="card card-info">
                            <div class="card-header">
                                <h4 class="card-title">Supplier Information</h4>
                            </div>
                            <hr style="margin: 0;"> --}}
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-xl-4 col-md-4 col-12">
                                                    <div class="form-group">
                                                        <label for="uid">SPV No</label>
                                                        <input type="number" class="form-control"
                                                               v-model="uid" id="uid"
                                                               name="uid" placeholder="Enter SPV No"
                                                               value="{{ old('uid') }}" readonly>
                                                        @if ($errors->has('uid'))
                                                            <small
                                                                class="text-danger">{{ $errors->first('uid') }}</small>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-xl-4 col-md-4 col-12">
                                                    <div class="form-group">
                                                        <label for="supplier_id">Group</label>
                                                        <select name="supplier_group_id"
                                                                id="supplier_group_id"
                                                                class="form-control bSelect"
                                                                v-model="supplier_group_id"
                                                                @change="fetch_supplier">
                                                            <option value="">Select Group</option>
                                                            @foreach($supplier_groups as $row)
                                                                <option
                                                                    value="{{ $row->id }}">{{ $row->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-md-4 col-12">
                                                    <div class="form-group">
                                                        <label for="supplier_id">Supplier</label>
                                                        <select name="supplier_id" id="supplier_id"
                                                                class="form-control bSelect"
                                                                v-model="supplier_id"
                                                                @change="fetch_due">
                                                            <option value="">Select Supplier</option>
                                                            <option :value="row.id"
                                                                    v-for="row in suppliers"
                                                                    v-html="row.name">
                                                            </option>

                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {{-- </div> --}}
                        {{-- <div class="card card-info">
                            <div class="card-header">
                                <h4 class="card-title">Payment Information</h4>
                            </div> --}}
                            <hr style="margin: 0;">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="date">Date</label>
                                                            <vuejs-datepicker v-model="date" name="date"
                                                                              placeholder="Select date"
                                                                              format="yyyy-MM-dd"></vuejs-datepicker>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12 col-md-12 col-12">
                                                        <div class="form-group">
                                                            <label for="credit_account_id">Payment
                                                                Account</label>
                                                            <select class="form-control select2"
                                                                    name="credit_account_id"
                                                                    id="credit_account_id">
                                                                <option value="">---Select Account---</option>
                                                                @foreach ($paymentAccounts as $row)
                                                                    <option
                                                                        value="{{ $row->id }}" {{ old('credit_account_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="col-xl-12 col-md-12 col-12">
                                                        <div class="form-group">
                                                            <label for="due">Due Amount</label>
                                                            <input type="number" class="form-control"
                                                                   id="due"
                                                                   v-model="due"
                                                                   name="due" placeholder="Enter due" readonly>

                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12 col-md-12 col-12">
                                                        <div class="form-group">
                                                            <label for="amount">Payment Amount</label>
                                                            <input type="number" class="form-control"
                                                                   id="amount"
                                                                   v-model="amount"
                                                                   name="amount" placeholder="Enter Amount"
                                                                   value="{{ old('amount') }}" @change="valid_amount">
                                                            @if ($errors->has('amount'))
                                                                <small
                                                                    class="text-danger">{{ $errors->first('amount') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12 col-md-12 col-12">
                                                        <div class="form-group">
                                                            <label for="payee_name">Reciever Name</label>
                                                            <input type="text" class="form-control"
                                                                   id="payee_name"
                                                                   name="payee_name"
                                                                   placeholder="Enter Reciever Name"
                                                                   value="{{ old('payee_name') }}">
                                                            @if ($errors->has('payee_name'))
                                                                <small
                                                                    class="text-danger">{{ $errors->first('payee_name') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12 col-md-12 col-12">
                                                        <div class="form-group">
                                                            <label for="reference_no">Reference No</label>
                                                            <input type="text" class="form-control"
                                                                   id="reference_no"
                                                                   name="reference_no"
                                                                   placeholder="Enter Reference No"
                                                                   value="{{ old('reference_no') }}">
                                                            @if ($errors->has('reference_no'))
                                                                <small
                                                                    class="text-danger">{{ $errors->first('reference_no') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12 col-md-12 col-12">
                                                        <div class="form-group">
                                                            <label for="narration">Description</label>
                                                            <textarea class="form-control" name="narration"
                                                                      id="narration" cols="" rows="3"
                                                                      placeholder="Enter Description">{{ old('narration') }}</textarea>
                                                            @if ($errors->has('narration'))
                                                                <small
                                                                    class="text-danger">{{ $errors->first('narration') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="float-right btn btn-info">Save
                                </button>
                            </div>
                            {{-- <div class="card-footer">
                                <button type="submit" class="float-right btn btn-info"><i class="fa fa-fw fa-lg fa-check-circle"></i>Submit
                                </button>
                            </div> --}}
                        {{-- </div> --}}
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->

@endsection
@section('css')

@endsection
@push('style')
    <style>
        .pageLoader {
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
@section('js')

@endsection
@push('script')
    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script src="{{ asset('vue-js/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="https://cms.diu.ac/vue/vuejs-datepicker.min.js"></script>
    <script>
        $(document).ready(function () {

            var vue = new Vue({
                el: '#vue_app',
                data: {
                    config: {

                        get_suppliers_info_by_group_id_url: "{{ url('fetch-suppliers-by-group-id') }}",
                        get_due_by_supplier_id_url: "{{ url('fetch-due-by-supplier-id') }}",
                    },
                    date: new Date(),
                    uid: {{$uid}},
                    supplier_group_id: '',
                    supplier_id: '',
                    amount: '',
                    due: '',
                    suppliers: [],
                    pageLoading: false
                },
                components: {
                    vuejsDatepicker
                },
                computed: {

                    subtotal: function () {
                        return this.selected_items.reduce((total, item) => {
                            return total + item.quantity * item.rate
                        }, 0)
                    },

                },
                methods: {

                    fetch_supplier() {

                        var vm = this;

                        var slug = vm.supplier_group_id;

                        if (slug) {
                            vm.pageLoading = true;
                            axios.get(this.config.get_suppliers_info_by_group_id_url + '/' + slug).then(function (response) {

                                // vm.selected_items = response.data.products;
                                vm.suppliers = response.data.suppliers;
                                vm.pageLoading = false;
                            }).catch(function (error) {

                                toastr.error('Something went to wrong', {
                                    closeButton: true,
                                    progressBar: true,
                                });

                                return false;

                            });
                        }

                    },

                    fetch_due() {

                        var vm = this;

                        var slug = vm.supplier_id;

                        if (slug) {
                            vm.pageLoading = true;
                            axios.get(this.config.get_due_by_supplier_id_url + '/' + slug).then(function (response) {

                                // vm.selected_items = response.data.products;
                                vm.due = response.data;
                                vm.pageLoading = false;
                            }).catch(function (error) {

                                toastr.error('Something went to wrong', {
                                    closeButton: true,
                                    progressBar: true,
                                });

                                return false;

                            });
                        }

                    },
                    valid_amount: function () {
                        var vm = this;
                        if (vm.amount <= 0) {
                            toastr.error('Quantity 0 or Negative not Allow', {
                                closeButton: true,
                                progressBar: true,
                            });
                            vm.amount = '';
                        }
                        if (vm.amount > vm.due) {
                            toastr.warning('Given Amount greater than Due Amount', {
                                closeButton: true,
                                progressBar: true,
                            });
                            vm.amount = vm.due;
                        }
                    },
                },

                updated() {
                    $('.bSelect').selectpicker('refresh');
                }

            });

            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });
        });
    </script>
@endpush
