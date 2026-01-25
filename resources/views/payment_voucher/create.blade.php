@extends('layouts.app')
@section('title', 'Payment Voucher Entry')

@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Payment Voucher'=>''
        ]
    @endphp
    <x-breadcrumb title='Payment Voucher' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                   <span v-if="pageLoading" class="pageLoader">
                       <img src="{{ asset('loading.gif') }}" alt="loading">
                  </span>
                <div class="col-lg-12 col-md-12">
                    <form action="{{ route('payment-vouchers.store') }}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="submission_token"
                               value="{{ session()->get('submission_token') ?? Str::random(40) }}">
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Payment Voucher (PV) Entry</h3>
                                <div class="card-tools">
                                    <a href="{{route('payment-vouchers.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-bars"
                                           aria-hidden="true"></i> &nbsp;
                                        See List
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="card-box">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="date">Date</label>
                                                <vuejs-datepicker v-model="date" name="date"
                                                                  placeholder="Select date"
                                                                  format="yyyy-MM-dd"></vuejs-datepicker>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="narration">Remark</label>
                                                <textarea class="form-control" name="narration" rows="1"
                                                          placeholder="Enter Narration"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">PV Line Item</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="from_account_id" class="control-label">Payment Mode ( @{{ from_ac_balance }})</label>
                                            <select class="form-control bSelect" name="from_account_id"
                                                    v-model="from_account_id" @change="fetchFromBalance">
                                                <option value="">Select One</option>
                                                @foreach ($chartOfAccounts as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="to_account_id" class="control-label">Expense Head</label>
                                            <select class="form-control bSelect" name="to_account_id"
                                                    v-model="to_account_id">
                                                <option value="">Select One</option>
                                                @foreach ($toChartOfAccounts as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="payee_name">Payee Name</label>
                                            <input type="text" class="form-control" id="payee_name"
                                                   name="payee_name" placeholder="Enter Payee Name"
                                                   v-model="payee_name">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="reference_no">Reference No</label>
                                            <input type="text" class="form-control" id="reference_no"
                                                   name="reference_no" placeholder="Enter Reference No"
                                                   v-model="reference_no">
                                            @if ($errors->has('reference_no'))
                                                <small class="text-danger">{{ $errors->first('reference_no') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                        <div class="form-group">
                                            <label for="amount">Amount</label>
                                            <input type="number" class="form-control" id="amount"
                                                   name="amount" placeholder="Enter Amount"
                                                   v-model="amount">
                                            @if ($errors->has('amount'))
                                                <small class="text-danger">{{ $errors->first('amount') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"
                                         style="margin-top: 30px;">
                                        <button type="button" class="btn btn-info btn-block"
                                                @click="data_input">Add
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                        <hr>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="bg-secondary">
                                                <tr>
                                                    <th style="width: 5%">#</th>
                                                    <th style="width: 20%">Payment Mode</th>
                                                    <th style="width: 20%">Expense Head</th>
                                                    <th style="width: 15%">Payee Name</th>
                                                    <th style="width: 20%">Reference No</th>
                                                    <th style="width: 15%">Amount</th>
                                                    <th style="width: 5%"></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(row, index) in selected_items">
                                                    <td>
                                                        @{{ ++index }}
                                                    </td>
                                                    <td>
                                                        @{{ row.from_account_name }}
                                                        <input type="hidden"
                                                               :name="'products['+index+'][credit_account_id]'"
                                                               class="form-control input-sm"
                                                               v-bind:value="row.from_account_id">

                                                    </td>
                                                    <td>
                                                        @{{ row.to_account_name }}
                                                        <input type="hidden"
                                                               :name="'products['+index+'][debit_account_id]'"
                                                               class="form-control input-sm"
                                                               v-bind:value="row.to_account_id">
                                                    </td>
                                                    <td>
                                                        @{{ row.payee_name }}
                                                        <input type="hidden" v-model="row.payee_name"
                                                               :name="'products['+index+'][payee_name]'"
                                                               class="form-control input-sm"
                                                               required>
                                                    </td>
                                                    <td>
                                                        @{{ row.reference_no }}
                                                        <input type="hidden" v-model="row.reference_no"
                                                               :name="'products['+index+'][reference_no]'"
                                                               class="form-control input-sm"
                                                               required>
                                                    </td>

                                                    <td>
                                                        @{{ row.amount }}
                                                        <input type="hidden" v-model="row.amount"
                                                               :name="'products['+index+'][amount]'"
                                                               class="form-control input-sm"
                                                               required>
                                                    </td>

                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                                @click="delete_row(row)"><i
                                                                class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <td colspan="7" style="background-color: #DDDCDC">

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">

                                                    </td>
                                                    <td>
                                                        Subtotal
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control input-sm"
                                                               name="subtotal" v-bind:value="subtotal"
                                                               readonly>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer" v-if="selected_items.length > 0">
                                <button class="float-right btn btn-primary" type="submit"><i
                                        class="fa fa-fw fa-lg fa-check-circle"></i>Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div> <!-- end col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
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
                        get_item_info_url: "{{ url('fetch-account-info') }}",
                        get_from_account_balance_data: "{{ url('fetch-from-account-balance') }}"
                    },
                    date: new Date(),
                    from_account_id: '',
                    to_account_id: '',
                    reference_no: '',
                    payee_name: '',
                    amount: '',
                    selected_items: [],
                    pageLoading: false,
                    uid: "22",
                    store_id: '',
                    from_ac_balance: ''
                },
                components: {
                    vuejsDatepicker
                },
                computed: {

                    subtotal: function () {
                        return this.selected_items.reduce((total, item) => {
                            return total + parseFloat(item.amount)
                        }, 0)
                    }
                },
                methods: {
                    fetchFromBalance() {
                        const vm = this;
                        const from_account_id = vm.from_account_id;
                        vm.pageLoading = true;
                        axios.get(this.config.get_from_account_balance_data + '/' + from_account_id).then(function (response) {
                            vm.from_ac_balance = response.data.from_ac_balance;
                            console.log(response.data.from_ac_balance);
                            vm.pageLoading = false;
                        })

                    },

                    data_input() {

                        let vm = this;
                        if (vm.from_account_id === vm.to_account_id) {
                            toastr.error('Please Select Different Account', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        } else {
                            if ((vm.from_ac_balance < vm.amount) || (vm.amount < 1)) {
                                toastr.error('Please Valid amount Input', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            }
                            let slug = vm.from_account_id;
                            let exists = vm.selected_items.some(function (field) {
                               return field.from_account_id == slug && field.to_account_id == vm.to_account_id
                            });

                            let to_account_id=vm.to_account_id

                            if (slug) {
                                vm.pageLoading = true;
                                axios.get(this.config.get_item_info_url + '/' + slug, {
                                    params: {
                                        from_account_id: vm.from_account_id,
                                        to_account_id: vm.to_account_id
                                    }
                                }).then(function (response) {
                                    let item_info = response.data;
                                    console.log(item_info);
                                    // Duplicate check: all fields must be same
                                    let isDuplicate = vm.selected_items.some(function (field) {
                                        return field.from_account_id == item_info.from_account_id &&
                                               field.to_account_id == item_info.to_account_id &&
                                               field.payee_name == vm.payee_name &&
                                               field.reference_no == vm.reference_no &&
                                               field.amount == vm.amount;
                                    });

                                    if (isDuplicate) {
                                        toastr.warning('It is already added!', {
                                            closeButton: true,
                                            progressBar: true,
                                        });
                                        vm.pageLoading = false;
                                        return false;
                                    }

                                    vm.selected_items.push({
                                        id: item_info.id,
                                        from_account_id: item_info.from_account_id,
                                        from_account_name: item_info.from_account_name,
                                        to_account_id: item_info.to_account_id,
                                        to_account_name: item_info.to_account_name,
                                        amount: vm.amount,
                                        payee_name: vm.payee_name,
                                        reference_no: vm.reference_no,
                                    });
                                    console.log(vm.selected_items);
                                    // Removed field resets to allow editing and re-adding
                                    vm.pageLoading = false;

                                }).catch(function (error) {

                                    toastr.error('Something went to wrong', {
                                        closeButton: true,
                                        progressBar: true,
                                    });

                                    return false;

                                });
                            }


                        }

                    },
                    delete_row: function (row) {
                        this.selected_items.splice(this.selected_items.indexOf(row), 1);
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
