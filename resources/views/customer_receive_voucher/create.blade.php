@extends('layouts.app')
@section('title', 'Customer Due Receive Voucher Entry')
@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Accounts Module'=>'',
            'Customer Receive Voucher'=>''
        ];
    @endphp
    <x-breadcrumb title='Customer Receive Voucher' :links="$links"/>
    <!-- Basic Inputs start -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <span v-if="pageLoading" class="pageLoader">
                    <img src="{{ asset('loading.gif') }}" alt="loading">
                </span>
                <div class="col-md-12">
                    <form action="{{ route('customer-receive-vouchers.store') }}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header bg-info">
                                <h4 class="card-title">Customer Due Receive Voucher (CRV) Entry</h4>
                                <div class="card-tools">
                                    <a href="{{route('customer-receive-vouchers.index')}}" class="btn btn-sm btn-primary">
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
                                <h3 class="card-title">CRV Line Item</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="debit_account_id">Receive Mode (To Account)</label>
                                            <select class="form-control bSelect" name="debit_account_id"
                                                    v-model="debit_account_id" ref="debit_account_select">
                                                <option value="">Select Account</option>
                                                @foreach ($paymentAccounts as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="customer_id">Customer (Total Due: @{{ total_customer_due }})</label>
                                            <select class="form-control bSelect" v-model="customer_id" @change="fetch_invoices" ref="customer_select">
                                                <option value="">Select Customer</option>
                                                @foreach($customers as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }} ({{$row->mobile}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                     <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="sale_id">Select Invoice (Due: @{{ due }})</label>
                                            <select class="form-control bSelect" v-model="sale_id" @change="set_due" id="invoice_select">
                                                <option value="">Select Invoice</option>
                                                <option v-for="inv in invoices" :value="inv.id">
                                                    @{{ inv.invoice_number }} (Due: @{{ inv.due_amount }})
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                        <div class="form-group">
                                            <label for="amount">Receive Amount</label>
                                            <input type="number" class="form-control" v-model="amount" placeholder="Enter Amount" @change="valid_amount">
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
                                                    <th style="width: 20%">Receive Mode</th>
                                                    <th style="width: 20%">Customer</th>
                                                    <th style="width: 20%">Invoice</th>
                                                    <th style="width: 15%">Amount</th>
                                                    <th style="width: 5%"></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(row, index) in selected_items">
                                                    <td>@{{ ++index }}</td>
                                                    <td>
                                                        @{{ row.debit_account_name }}
                                                        <input type="hidden" :name="'products['+index+'][debit_account_id]'" :value="row.debit_account_id">
                                                    </td>
                                                    <td>
                                                        @{{ row.customer_name }}
                                                        <input type="hidden" :name="'products['+index+'][customer_id]'" :value="row.customer_id">
                                                    </td>
                                                    <td>
                                                        @{{ row.invoice_number }}
                                                        <input type="hidden" :name="'products['+index+'][sale_id]'" :value="row.sale_id">
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
                                                    <td colspan="6" style="background-color: #DDDCDC"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"></td>
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
                        get_invoices_url: "{{ url('fetch-due-invoices-by-customer-id') }}",
                    },
                    date: new Date(),
                    customer_id: '',
                    debit_account_id: '',
                    sale_id: '',
                    amount: '',
                    due: 0,
                    invoices: [],
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
                    fetch_invoices() {
                        var vm = this;
                        var slug = vm.customer_id;
                        vm.invoices = [];
                        vm.sale_id = '';
                        vm.due = 0;
                        vm.total_customer_due = 0;
                        
                        if (slug) {
                            vm.pageLoading = true;
                            axios.get(this.config.get_invoices_url + '/' + slug).then(function (response) {
                                vm.invoices = response.data.invoices;
                                // Calculate Total Customer Due
                                vm.total_customer_due = vm.invoices.reduce((acc, inv) => acc + (parseFloat(inv.due_amount) || 0), 0);
                                
                                vm.pageLoading = false;
                                // Need to refresh selectpicker
                                setTimeout(function() {
                                    $('#invoice_select').selectpicker('refresh');
                                }, 100);
                            }).catch(function (error) {
                                toastr.error('Something went wrong fetching invoices');
                                vm.pageLoading = false;
                            });
                        } else {
                            setTimeout(function() {
                                $('#invoice_select').selectpicker('refresh');
                            }, 100);
                        }
                    },
                    set_due() {
                        var vm = this;
                        if(vm.sale_id) {
                            var inv = vm.invoices.find(item => item.id == vm.sale_id);
                            if(inv) {
                                vm.due = inv.due_amount;
                            } else {
                                vm.due = 0;
                            }
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
                        if (vm.due > 0 && vm.amount > vm.due) {
                             toastr.warning('Amount greater than Due');
                        }
                    },
                    data_input() {
                        let vm = this;
                        if (!vm.debit_account_id || !vm.customer_id || !vm.sale_id || !vm.amount) {
                            toastr.error('Please fill all required fields');
                            return;
                        }
                        if (vm.amount <= 0) {
                            toastr.error('Invalid Amount');
                            return;
                        }
                        
                         // Check Duplication
                        let exists = vm.selected_items.some(function (item) {
                            return item.sale_id == vm.sale_id;
                        });
                        
                        if (exists) {
                            toastr.error('This Invoice is already added to the list.');
                             return;
                        }

                        // Get Names for Display
                        let debit_name = vm.$refs.debit_account_select.options[vm.$refs.debit_account_select.selectedIndex].text;
                        
                        let customer_name = vm.$refs.customer_select.options[vm.$refs.customer_select.selectedIndex].text;
                        
                        let inv = vm.invoices.find(item => item.id == vm.sale_id);
                        let invoice_number = inv ? inv.invoice_number : vm.sale_id;

                        vm.selected_items.push({
                            debit_account_id: vm.debit_account_id,
                            debit_account_name: debit_name,
                            customer_id: vm.customer_id,
                            customer_name: customer_name,
                            sale_id: vm.sale_id,
                            invoice_number: invoice_number,
                            amount: vm.amount
                        });

                        // Reset fields
                        // Keep customer and mode? 
                        // Maybe reset invoice and amount only?
                        // User might want to add another invoice for same customer.
                        // So I won't reset customer, but will reset sale_id and amount.
                        vm.sale_id = '';
                        vm.amount = '';
                        vm.due = 0;
                        
                         setTimeout(function() {
                            $('#invoice_select').selectpicker('refresh');
                        }, 50);
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
