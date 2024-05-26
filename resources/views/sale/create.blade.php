@extends('layouts.app')
@section('title')
    Sale Entry
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Sale list'=>''
        ]
    @endphp
    <x-breadcrumb title='Sale Entry' :links="$links"/>



    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <div class="col-lg-12 col-md-12">

                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Sale Entry</h3>
                            <div class="card-tools">
                                <a href="{{route('sales.index')}}">
                                    <button class="btn btn-sm btn-primary">
                                        <i class="fa fa-list" aria-hidden="true"></i> &nbsp;Sale List
                                    </button>
                                </a>
                            </div>
                        </div>


                        <form action="{{ route('sales.store') }}" method="POST" class="">
                            @csrf
                            <div class="card-body">
                                <div class="card-box">
                                    <div id="">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label for="date">Date</label>
                                                    <vuejs-datepicker v-model="date" name="date"
                                                                      placeholder="Select date"
                                                                      format="yyyy-MM-dd"
                                                                      @closed="getStoreData()"></vuejs-datepicker>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label for="">Current Store</label>
                                                    <select name="store_id" id="" class="form-control"
                                                            @if($user_store) disabled @endif @change="getStoreData()"
                                                            v-model="store_id">
                                                        <option value="">None</option>
                                                        @foreach($stores as $store)
                                                            <option value="{{$store->id}}">{{ $store->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="customer_id">Invoice Number</label>
                                                    <input type="text" class="form-control"
                                                           placeholder="Enter Invoice Number" v-model="invoice_number">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="customer_id">Customer Number</label>
                                                    <input type="text" class="form-control"
                                                           placeholder="Enter Customer Number and Press Enter"
                                                           v-model="customerNumber"
                                                           @keydown.enter="getCustomerInfo" name="customer_number">
                                                    {{--                                                    <select name="customer_id" id="customer_id"--}}
                                                    {{--                                                            class="form-control bSelect" v-model="customer_id">--}}
                                                    {{--                                                        <option value="">Select One</option>--}}
                                                    {{--                                                        @foreach($customers as $customer)--}}
                                                    {{--                                                            <option--}}
                                                    {{--                                                                value="{{ $customer->id }}">{{ $customer->name }}</option>--}}
                                                    {{--                                                        @endforeach--}}
                                                    {{--                                                    </select>--}}
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="store_id">Customer Name</label>
                                                    <input type="text" v-model="customer.name" class="form-control"
                                                           disabled>
                                                    {{--                                                    <select name="store_id" id="store_id"--}}
                                                    {{--                                                            class="form-control bSelect"--}}
                                                    {{--                                                            v-model="store_id" required>--}}
                                                    {{--                                                        <option value="">Select One</option>--}}
                                                    {{--                                                        @foreach($stores as $row)--}}
                                                    {{--                                                            <option--}}
                                                    {{--                                                                value="{{ $row->id }}">{{ $row->name }}</option>--}}
                                                    {{--                                                        @endforeach--}}
                                                    {{--                                                    </select>--}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label for="category_id" class="control-label">Group</label>
                                                    <select class="form-control bSelect" name="category_id"
                                                            v-model="category_id" @change="fetch_item">
                                                        <option value="">Select One</option>
                                                        @foreach ($groups as $category)
                                                            <option
                                                                value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label for="item_id">Item</label>
                                                    <select name="item_id" id="item_id"
                                                            class="form-control bSelect" v-model="item_id">
                                                        <option value="">Select one</option>

                                                        <option :value="row.id" v-for="row in products"
                                                                v-html="row.name">
                                                        </option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="margin-top: 26px;">
                                                <button type="button" class="btn btn-info btn-block"
                                                        @click="data_input">Add
                                                </button>
                                            </div>

                                            <br>
                                            <br>
                                            <br>
                                            <br>

                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                                <hr>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Group</th>
                                                            <th>Item</th>
                                                            <th>Unit</th>
                                                            <th>Balance Qty</th>
                                                            <th>Selling Price</th>
                                                            <th>Quantity</th>
                                                            <th>Discount</th>
                                                            <th>Item total</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(row, index) in items">
                                                            <td>
                                                                <button type="button" class="btn btn-danger"
                                                                        @click="delete_row(row)"><i
                                                                        class="fa fa-trash"></i></button>
                                                            </td>
                                                            <td>
                                                                @{{ row.group }}
                                                            </td>
                                                            <td>
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][item_id]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.item_id">
                                                                <input type="text" class="form-control input-sm"
                                                                       v-bind:value="row.product_name" readonly>
                                                            </td>
                                                            <td>
                                                                @{{ row.unit }}
                                                            </td>
                                                            <td>

                                                                <input type="text" :name="'products['+index+'][stock]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.stock" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="number" v-model="row.price"
                                                                       :name="'products['+index+'][sale_price]'"
                                                                       class="form-control input-sm"
                                                                       @change="itemtotal(row)" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="number" v-model="row.quantity"
                                                                       :name="'products['+index+'][quantity]'"
                                                                       class="form-control input-sm"
                                                                       @change="valid(row)" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" v-model="row.product_discount"
                                                                       :name="'products['+index+'][product_discount]'"
                                                                       class="form-control input-sm" required>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control input-sm"
                                                                       v-bind:value="itemtotal(row)" readonly>
                                                            </td>

                                                        </tr>

                                                        </tbody>
                                                        <tfoot>

                                                        <tr>
                                                            <td colspan="7">

                                                            </td>
                                                            <td>
                                                                SubTotal
                                                            </td>
                                                            <td>
                                                                <input type="text" name="subtotal"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="total_bill" readonly>

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7">

                                                            </td>
                                                            <td>
                                                                Discount
                                                            </td>
                                                            <td>
                                                                <input type="text" name="discount"
                                                                       class="form-control input-sm" v-model="allDiscountAmount" disabled>

                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="7">

                                                            </td>
                                                            <td>
                                                                Grand Total
                                                            </td>
                                                            <td>
                                                                <input type="text" name="grandtotal"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="total_payable_bill" readonly>

                                                            </td>
                                                        </tr>
                                                        <tfoot>
                                                    </table>
                                                </div>
                                                <hr>
                                                <div class="table-responsive">
                                                    <div class="row">
                                                        <div class="col-7">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Method</th>
                                                                    <th>Amount</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr v-for="(payment, index) in paymentMethods">
                                                                    <td>
                                                                        <button type="button" class="btn btn-danger"
                                                                                @click="deletePaymentMethod(payment)"><i
                                                                                class="fa fa-trash"></i></button>
                                                                    </td>
                                                                    <td>
                                                                        <select v-model="payment.method"
                                                                                :name="'payment_methods['+index+'][method]'"
                                                                                class="form-control">
                                                                            <option value="cash">Cash</option>
                                                                            <option value="bkash">Bkash</option>
                                                                            <option value="point">Redeem Point</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" v-model="payment.amount"
                                                                               :step="payment.method == 'point' ? 100 : 1"
                                                                               :name="'payment_methods['+index+'][amount]'"
                                                                               @key.press="checkPointInput"
                                                                               class="form-control">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="3">
                                                                        <button class="btn btn-sm btn-secondary"
                                                                                type="button"
                                                                                @click="addMorePaymentMethod"> Add
                                                                            Another Payment Method
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-5">
                                                            <table class="table">
                                                                <tr>
                                                                    <th>Receive Amount</th>
                                                                    <td>
                                                                        {{--                                                                        <input type="text" name="receive_amount"--}}
                                                                        {{--                                                                               class="form-control input-sm"--}}
                                                                        {{--                                                                               v-model="receive_amount" disabled>--}}
                                                                        <input type="text" name="receive_amount"
                                                                               class="form-control input-sm"
                                                                               v-model="total_paying" disabled>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Change Amount</th>
                                                                    <td>
                                                                        {{--                                                                        <input type="text" name="receive_amount"--}}
                                                                        {{--                                                                               class="form-control input-sm"--}}
                                                                        {{--                                                                               v-model="change_amount" disabled>--}}
                                                                        <input type="text" name="change_amount"
                                                                               class="form-control input-sm"
                                                                               v-model="cash_change" disabled>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 v-if="items.length > 0">
                                                <div class="text-right col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <textarea class="form-control" name="comments" rows="5"
                                                                  placeholder="Enter Comments"></textarea>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>

                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right" v-if="items.length > 0">
                                <button class="float-right btn btn-primary" type="submit"><i
                                        class="fa fa-fw fa-lg fa-check-circle"></i>Submit
                                </button>
                            </div>
                        </form>
                    </div>


                </div> <!-- end col -->
            </div>
            <!-- /.row -->

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@push('style')
    <style>
        .categoryLoader {
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
                        get_items_info_by_group_id_url: "{{ url('fetch-items-by-group-id') }}",
                        get_product_info_url: "{{ url('fetch-item-by-id-for-sale') }}",
                        get_customer_url: "{{ url('pos-customer-by-number') }}",
                    },
                    customer_id: '',
                    store_id: "{{ $user_store && $user_store->id }}",
                    category_id: '',
                    item_id: '',
                    products: [],
                    items: [],
                    quantity: '',
                    Stock_quantity: 0,
                    price: 0,
                    discount: 0,
                    product_discount: 0,
                    receive_amount: 0,
                    selling_price: 0,
                    customerNumber: '',
                    customer: {},
                    date: new Date(),
                    invoice_number: "{{ $invoice_number ?? 'Please Select Store First' }}",
                    paymentMethods: [{
                        amount: 0,
                        method: 'cash'
                    }],
                    couponCodeDiscountAmount: 0,
                    special_discount_amount: 0,
                    total_discount_amount: 0,

                },
                components: {
                    vuejsDatepicker
                },
                computed: {
                    subtotal: function () {
                        return this.items.reduce((total, item) => {
                            return total + ((item.quantity * item.price) - item.product_discount)
                        }, 0)
                    },
                    grandtotal: function () {
                        return this.subtotal - this.discount
                    },
                    change_amount: function () {
                        return this.grandtotal - this.receive_amount
                    },
                    total_bill: function () {
                        return this.items.reduce((total, item) => {
                            return total + ((item.quantity * item.price))
                        }, 0)
                    },
                    total_paying: function () {
                        let amount = this.paymentMethods.reduce((total, item) => {
                            return Number(total) + Number(item.method ? item.amount : 0)
                        }, 0)
                        if (amount > 0) {
                            if (this.items.length < 1) {
                                toastr.error('Please Add Items', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return 0;
                            }
                        }
                        return amount
                    },
                    pay_left: function () {
                        return this.total_paying > this.total_due ? 0 : this.total_due - this.total_paying
                    },
                    cash_change: function () {
                        return this.total_paying > this.total_due ? this.total_paying - this.total_due : 0
                    },
                    total_payable_bill: function () {
                        var vm = this
                        return (this.total_bill - vm.couponCodeDiscountAmount - this.allDiscountAmount)
                    },
                    total_due: function () {
                        return this.total_payable_bill
                    },
                    productWiseDiscount: function () {
                        return this.items.reduce((total, item) => {
                            return total + Number(item.product_discount)
                        }, 0)
                    },
                    allDiscountAmount: function () {
                        var vm = this
                        return Number(vm.total_discount_amount) + Number(vm.special_discount_amount) + Number(this.productWiseDiscount)
                    }
                },
                methods: {
                    fetch_item() {
                        var vm = this;
                        var slug = vm.category_id;
                        if (slug) {
                            axios.get(this.config.get_items_info_by_group_id_url + '/' + slug).then(function (response) {
                                vm.products = response.data.products;
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
                    data_input() {
                        var vm = this;
                        if (!vm.item_id) {
                            toastr.error('Enter product', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        } else {
                            var slug = vm.item_id;
                            if (slug) {
                                axios.get(this.config.get_product_info_url + '/' + slug).then(function (response) {
                                    product_details = response.data;
                                    vm.items.push({
                                        item_id: vm.item_id,
                                        group: product_details.group,
                                        product_name: product_details.name,
                                        unit: product_details.unit,
                                        stock: product_details.balance_qty,
                                        price: product_details.price,
                                        sale_price: product_details.price,
                                        quantity: '',
                                        product_discount: 0,
                                        subtotal: 0,
                                    });
                                    vm.item_id = '';
                                    vm.category_id = '';

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
                        this.items.splice(this.items.indexOf(row), 1);
                    },
                    itemtotal: function (index) {

                        return (index.quantity * index.price) - index.product_discount;

                    },
                    valid: function (index) {

                        if (index.quantity > index.stock) {
                            index.quantity = index.stock;
                        }
                        if (index.quantity <= 0) {
                            index.quantity = '';
                        }
                    },
                    getCustomerInfo() {
                        var vm = this;
                        vm.customer = {}
                        axios.get(this.config.get_customer_url, {
                            params: {
                                number: this.customerNumber
                            }
                        }).then(function (response) {
                            vm.customer = (response.data);
                            if (vm.customer.name) {
                                vm.customer.name = vm.customer.name + '  (' + vm.customer.current_point + '  point)'
                            }
                        }).catch(function (error) {
                            toastr.error(error, {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });
                    },
                    getStoreData() {
                        const vm = this
                        if (!vm.store_id) {
                            vm.invoice_number = 'Please Select Store First'
                            toastr.error('Please Select valid Store', {
                                closeButton: true,
                                progressBar: true,
                            });
                        }
                        axios.get('/invoice-by-store/' + vm.store_id, {
                            params: {
                                date: vm.date
                            }
                        }).then((response) => {
                                vm.invoice_number = response.data
                            })
                    },
                    deletePaymentMethod: function (row) {
                        this.paymentMethods.splice(this.paymentMethods.indexOf(row), 1);
                    },
                    addMorePaymentMethod() {
                        this.paymentMethods.push({amount: 0, method: ''})
                    },
                },
                updated() {
                    $('.bSelect').selectpicker('refresh');
                },
            });
            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });
        });
    </script>
@endpush
