@extends('layouts.app')
@section('title', 'Supplier Payment Voucher Entry')
@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Accounts Module'=>'',
            'Supplier Payment Voucher'=>''
        ];
    @endphp
    <x-breadcrumb title='Supplier Payment Voucher' :links="$links"/>
    <!-- Basic Inputs start -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <span v-if="pageLoading" class="pageLoader">
                    <img src="{{ asset('loading.gif') }}" alt="loading">
                </span>
                <div class="col-md-12">
                    <form action="{{ route('supplier-vouchers.store') }}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header bg-info">
                                <h4 class="card-title">Supplier Payment Voucher (SPV) Entry</h4>
                                <div class="card-tools">
                                    <a href="{{route('supplier-vouchers.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-bars" aria-hidden="true"></i> &nbsp;See List
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
                                <h3 class="card-title">SPV Line Item</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="credit_account_id">Payment Mode</label>
                                            <select class="form-control bSelect" name="credit_account_id"
                                                    v-model="credit_account_id" ref="credit_account_select">
                                                <option value="">Select Account</option>
                                                @foreach ($paymentAccounts as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="supplier_group_id">Group</label>
                                            <select class="form-control bSelect" v-model="supplier_group_id" @change="fetch_supplier">
                                                <option value="">Select Group</option>
                                                @foreach($supplier_groups as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="supplier_id">Supplier (Due: @{{ due }})</label>
                                            <select class="form-control bSelect" v-model="supplier_id" @change="fetch_due" ref="supplier_select">
                                                <option value="">Select Supplier</option>
                                                <option v-for="row in suppliers" :value="row.id">@{{ row.name }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="amount">Amount</label>
                                            <input type="number" class="form-control" v-model="amount" placeholder="Enter Amount" @change="valid_amount">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="payee_name">Payee Name</label>
                                            <input type="text" class="form-control" v-model="payee_name" placeholder="Enter Payee Name">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="reference_no">Reference No</label>
                                            <input type="text" class="form-control" v-model="reference_no" placeholder="Enter Reference No">
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12" style="margin-top: 30px;">
                                        <button type="button" class="btn btn-info btn-block" @click="data_input">Add</button>
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
                                                    <th style="width: 20%">Supplier</th>
                                                    <th style="width: 15%">Payee Name</th>
                                                    <th style="width: 20%">Reference No</th>
                                                    <th style="width: 15%">Amount</th>
                                                    <th style="width: 5%"></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(row, index) in selected_items">
                                                    <td>@{{ ++index }}</td>
                                                    <td>
                                                        @{{ row.credit_account_name }}
                                                        <input type="hidden" :name="'products['+index+'][credit_account_id]'" :value="row.credit_account_id">
                                                    </td>
                                                    <td>
                                                        @{{ row.supplier_name }}
                                                        <input type="hidden" :name="'products['+index+'][supplier_id]'" :value="row.supplier_id">
                                                    </td>
                                                    <td>
                                                        @{{ row.payee_name }}
                                                        <input type="hidden" :name="'products['+index+'][payee_name]'" :value="row.payee_name">
                                                    </td>
                                                    <td>
                                                        @{{ row.reference_no }}
                                                        <input type="hidden" :name="'products['+index+'][reference_no]'" :value="row.reference_no">
                                                    </td>
                                                    <td>
                                                        @{{ row.amount }}
                                                        <input type="hidden" :name="'products['+index+'][amount]'" :value="row.amount">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger" @click="delete_row(row)"><i class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <td colspan="7" style="background-color: #DDDCDC"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4"></td>
                                                    <td>Subtotal</td>
                                                    <td>
                                                        <input type="text" class="form-control input-sm" name="subtotal" :value="subtotal" readonly>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer" v-if="selected_items.length > 0">
                                <button class="float-right btn btn-primary" type="submit">
                                    <i class="fa fa-fw fa-lg fa-check-circle"></i>Submit
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
            top: 50%;
            right: 40%;
            transform: translate(-50%, -50%);
            color: red;
            z-index: 999;
        }
        input[placeholder="Select date"] {
            display: block; width: 100%; height: calc(2.25rem + 2px); padding: .375rem .75rem;
            font-size: 1rem; color: #495057; background-color: #fff; border: 1px solid #ced4da;
            border-radius: .25rem;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('vue-js/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endpush

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
                    supplier_group_id: '',
                    supplier_id: '',
                    credit_account_id: '',
                    amount: '',
                    due: 0,
                    payee_name: '',
                    reference_no: '',
                    suppliers: [],
                    selected_items: [],
                    pageLoading: false
                },
                components: { vuejsDatepicker },
                computed: {
                    subtotal: function () {
                        return this.selected_items.reduce((total, item) => {
                            return total + parseFloat(item.amount)
                        }, 0)
                    }
                },
                methods: {
                    fetch_supplier() {
                        var vm = this;
                        var slug = vm.supplier_group_id;
                        if (slug) {
                            vm.pageLoading = true;
                            axios.get(this.config.get_suppliers_info_by_group_id_url + '/' + slug).then(function (response) {
                                vm.suppliers = response.data.suppliers;
                                vm.pageLoading = false;
                                // Need to refresh local selectpicker manually if needed, or rely on updated hook
                            }).catch(function (error) {
                                toastr.error('Something went wrong');
                                vm.pageLoading = false;
                            });
                        }
                    },
                    fetch_due() {
                        var vm = this;
                        var slug = vm.supplier_id;
                        if (slug) {
                            vm.pageLoading = true;
                            axios.get(this.config.get_due_by_supplier_id_url + '/' + slug).then(function (response) {
                                vm.due = response.data;
                                vm.pageLoading = false;
                            }).catch(function (error) {
                                toastr.error('Something went wrong');
                                vm.pageLoading = false;
                            });
                        } else {
                            vm.due = 0;
                        }
                    },
                    valid_amount() {
                         var vm = this;
                        if (vm.amount < 0) {
                             toastr.error('Negative amount not allowed');
                             vm.amount = '';
                             return;
                        }
                        // Removing strict Due check to allow overpayment or advance?
                        // Prev code: if (vm.amount > vm.due) warning and reset.
                        // I will keep the warning but maybe NOT reset? Or reset as per previous behavior.
                        // User likely wants to clear due.
                        if (vm.due > 0 && vm.amount > vm.due) {
                             toastr.warning('Amount greater than Due');
                             // vm.amount = vm.due; // Optional: Force limit.
                             // I'll leave it open but warn.
                        }
                    },
                    data_input() {
                        let vm = this;
                        if (!vm.credit_account_id || !vm.supplier_id || !vm.amount || !vm.payee_name) {
                            toastr.error('Please fill all required fields');
                            return;
                        }
                        if (vm.amount <= 0) {
                            toastr.error('Invalid Amount');
                            return;
                        }

                        // Get Names for Display
                        let credit_name = "";
                        // We need to find the name from the select options.
                        // Since we are inside Vue, we can't easily query the DOM unless we use refs or querySelector.
                        // Easier: use querySelector on the Select element by name or ref.
                        let processed_credit = vm.$refs.credit_account_select.options[vm.$refs.credit_account_select.selectedIndex].text;
                        
                        // For Supplier
                        // Provide a way to get name. 
                        // vm.suppliers has the list.
                        let supplier_obj = vm.suppliers.find(s => s.id == vm.supplier_id);
                        let supplier_name = supplier_obj ? supplier_obj.name : '';

                        vm.selected_items.push({
                            credit_account_id: vm.credit_account_id,
                            credit_account_name: processed_credit,
                            supplier_id: vm.supplier_id,
                            supplier_name: supplier_name,
                            amount: vm.amount,
                            payee_name: vm.payee_name,
                            reference_no: vm.reference_no
                        });

                        // Reset fields
                        vm.supplier_id = '';
                        vm.supplier_group_id = ''; // Optional reset
                        vm.suppliers = []; // Clear suppliers list? Or keep group? Maybe keep group.
                        vm.due = 0;
                        vm.amount = '';
                        vm.payee_name = '';
                        vm.reference_no = '';
                        
                        // Keep credit account? Usually yes.
                    },
                    delete_row(row) {
                        this.selected_items.splice(this.selected_items.indexOf(row), 1);
                    }
                },
                updated() {
                    $('.bSelect').selectpicker('refresh');
                }
            });
            $('.bSelect').selectpicker({ liveSearch: true, size: 5 });
        });
    </script>
@endpush
