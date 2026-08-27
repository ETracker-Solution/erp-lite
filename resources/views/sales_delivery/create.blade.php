@extends('layouts.app')
@section('title', 'Sales Delivery')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Sales Delivery' => route('sales-deliveries.index'),
            'Create' => '',
        ];
        $prefillSaleId = $prefill_sale_id ?? (request('sale_id') ? (string) request('sale_id') : '');
    @endphp
    <x-breadcrumb title="Sales Delivery" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Make Sales Delivery</h3>
                            <div class="card-tools">
                                <a href="{{ route('sales-deliveries.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                            </div>
                        </div>

                        <form id="deliveryForm" action="{{ route('sales-deliveries.store') }}" method="POST"
                              class="prevent-enter-submit">
                            @csrf
                            <input type="hidden" name="submission_token"
                                   value="{{ session()->get('submission_token') ?? Str::random(40) }}">
                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    Deliver a pending other-outlet / pre-order sale and collect remaining payment.
                                </p>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date">Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="date" name="date"
                                                   v-model="date" required @change="getStoreData()">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Current Store <span class="text-danger">*</span></label>
                                            @if($user_store)
                                                <select class="form-control" disabled v-model="store_id"
                                                        @change="getStoreData()">
                                                    @foreach($stores as $store)
                                                        <option value="{{ $store->id }}"
                                                            {{ $user_store->id == $store->id ? 'selected' : '' }}>
                                                            {{ $store->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="store_id" :value="store_id">
                                            @else
                                                <select name="store_id" class="form-control" v-model="store_id"
                                                        @change="getStoreData()" required>
                                                    <option value="">Select store</option>
                                                    @foreach($stores as $store)
                                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Delivery Invoice <span class="text-danger">*</span></label>
                                            <select name="sale_id" class="form-control select2" v-model="sale_id" required>
                                                <option value="">Select invoice</option>
                                                <option :value="row.id" v-for="row in salesData" :key="row.id">
                                                    @{{ row.invoice_number }}@{{ row.date ? ' — ' + row.date : '' }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="customer_number">Customer Number</label>
                                            <input type="text" class="form-control"
                                                   placeholder="Enter number and press Enter"
                                                   v-model="customerNumber"
                                                   @keydown.enter.prevent="getCustomerInfo" name="customer_number">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Customer Name</label>
                                            <input type="text" v-model="customer.name" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                                        <div class="row" v-if="items && items.length > 0">
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
                                                                       @change="valid(row)" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="number" v-model="row.product_discount"
                                                                       :name="'products['+index+'][product_discount]'"
                                                                       class="form-control input-sm" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control input-sm"
                                                                       v-bind:value="itemtotal(row)" readonly>
                                                            </td>

                                                        </tr>

                                                        </tbody>
                                                        <tfoot>

                                                        {{--                                                        <tr>--}}
                                                        {{--                                                            <td colspan="7">--}}

                                                        {{--                                                            </td>--}}
                                                        {{--                                                            <td>--}}
                                                        {{--                                                                SubTotal--}}
                                                        {{--                                                            </td>--}}
                                                        {{--                                                            <td>--}}
                                                        {{--                                                                <input type="text" name="subtotal"--}}
                                                        {{--                                                                       class="form-control input-sm"--}}
                                                        {{--                                                                       v-bind:value="total_bill" readonly>--}}

                                                        {{--                                                            </td>--}}
                                                        {{--                                                        </tr>--}}
                                                        {{--                                                        <tr>--}}
                                                        {{--                                                            <td colspan="7">--}}

                                                        {{--                                                            </td>--}}
                                                        {{--                                                            <td>--}}
                                                        {{--                                                                Discount--}}
                                                        {{--                                                            </td>--}}
                                                        {{--                                                            <td>--}}
                                                        {{--                                                                <input type="text" name="discount"--}}
                                                        {{--                                                                       class="form-control input-sm"--}}
                                                        {{--                                                                       v-model="allDiscountAmount" disabled>--}}

                                                        {{--                                                            </td>--}}
                                                        {{--                                                        </tr>--}}

                                                        {{--                                                        <tr>--}}
                                                        {{--                                                            <td colspan="7">--}}

                                                        {{--                                                            </td>--}}
                                                        {{--                                                            <td>--}}
                                                        {{--                                                                Grand Total--}}
                                                        {{--                                                            </td>--}}
                                                        {{--                                                            <td>--}}
                                                        {{--                                                                <input type="text" name="grandtotal"--}}
                                                        {{--                                                                       class="form-control input-sm"--}}
                                                        {{--                                                                       v-bind:value="total_payable_bill" readonly>--}}
                                                        {{--                                                            </td>--}}
                                                        {{--                                                        </tr>--}}
                                                        <tr>
                                                            <td colspan="7">

                                                            </td>
                                                            <td>
                                                                Delivery Charge
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="delivery_charge" readonly>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7">

                                                            </td>
                                                            <td>
                                                                Additional Charge
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="additional_charge" readonly>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7">

                                                            </td>
                                                            <td>
                                                                Total Discount
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="orderDiscount" readonly>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7">

                                                            </td>
                                                            <td>
                                                                Grand Total
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="orderAmount" readonly>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7">

                                                            </td>
                                                            <td>
                                                                Paid
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="oldPaid" readonly>
                                                            </td>
                                                        </tr>
                                                        <tr style>
                                                            <td colspan="7">

                                                            </td>
                                                            <td style="background-color: rgb(194, 43, 43); color: white; font-weight: 700; font-size: 20px">
                                                                Due
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="receivable_amount" readonly>
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
                                                                            <option value="nagad">Nagad</option>
                                                                            <option value="DBBL">DBBL</option>
                                                                            <option value="UCB">UCB</option>
                                                                            <option value="rocket">Rocket</option>
                                                                            <option value="upay">Upay</option>
                                                                            <option value="nexus">Nexus</option>
                                                                            <option value="pbl">PBL POS</option>
                                                                            <option value="PBLQR">PBL QR</option>
                                                                            <option value="FOODIE">FOODIE</option>
                                                                            <option value="due">Due Sale</option>
                                                                            <option value="FoodPanda">Food Panda</option>
                                                                            <option value="CityBank">City Bank</option>
                                                                            <option value="point">Redeem Point</option>
                                                                            <option value="exchange">Exchange</option>
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
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right erp-save-bar" v-if="items.length > 0">
                                <button class="float-right btn btn-primary"
                                        type="button"
                                        :disabled="isSubmitting"
                                        @click="submitForm"><i
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
    <script>
        $(document).ready(function () {
            var vue = new Vue({
                el: '#vue_app',
                data: {
                    config: {
                        get_items_info_by_group_id_url: "{{ url('fetch-items-by-group-id') }}",
                        get_product_info_url: "{{ url('fetch-item-by-id-for-sale') }}",
                        get_customer_url: "{{ url('pos-customer-by-number') }}",
                        get_data_by_invoice: "{{ url('fetch-data-by-sale-id-for-sale') }}"
                    },
                    customer_id: '',
                    store_id: @json((string) ($prefill_store_id ?? '')),
                    sale_id: @json($prefillSaleId),
                    prefillSale: @json($prefill_sale),
                    delivery_point_id: '',
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
                    delivery_point_receive_amount: 0,
                    selling_price: 0,
                    customerNumber: '',
                    customer: {},
                    date: "{{ date('Y-m-d') }}",
                    paymentMethods: [{
                        amount: 0,
                        method: 'cash'
                    }],
                    couponCodeDiscountAmount: 0,
                    special_discount_amount: 0,
                    total_discount_amount: 0,
                    oldPaid: 0,
                    receivable_amount: 0,
                    orderDiscount: 0,
                    delivery_charge: 0,
                    additional_charge: 0,
                    orderAmount: 0,
                    isSubmitting: false,
                    salesData: []
                },
                mounted: function () {
                    const self = this;

                    $('.select2').select2();
                    $('.select2').on('change', function () {
                        self.sale_id = $(this).val();
                        self.getAllData();
                    });
                    this.preventEnterSubmit();

                    if (this.sale_id) {
                        this.bootstrapPrefill();
                    } else if (this.store_id) {
                        this.getStoreData();
                    }
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
                        return Math.round(this.total_bill - vm.couponCodeDiscountAmount - this.allDiscountAmount)
                    },
                    total_due: function () {
                        return this.receivable_amount
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
                    getAllData() {
                        const vm = this;
                        if (!vm.store_id) {
                            toastr.warning('Please Select Store', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                            vm.sale_id = ''
                        }
                        const sale_id = vm.sale_id;
                        if (sale_id) {
                            vm.items = [];
                            axios.get(this.config.get_data_by_invoice + '/' + sale_id).then(function (response) {
                                const resData = response.data;
                                vm.products = resData.items;
                                vm.products.map((product) => {
                                    vm.fetch_item(product.product_id, product.quantity, sale_id)
                                })
                                if (response.data.customer) {
                                    vm.customerNumber = resData.customer.mobile
                                    vm.getCustomerInfo()
                                }
                                vm.orderDiscount = resData.discount
                                vm.receivable_amount = resData.grand_total - resData.receive_amount
                                vm.receivable_amount = vm.receivable_amount > 0 ? vm.receivable_amount : 0
                                vm.receivable_amount = Math.round(vm.receivable_amount)
                                vm.oldPaid = vm.receivable_amount > 0 ? resData.receive_amount : resData.grand_total
                                vm.delivery_charge = resData.delivery_charge
                                vm.additional_charge = resData.additional_charge
                                vm.orderAmount = resData.grand_total
                            }).catch(function (error) {
                                toastr.error('Something went to wrong', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            });
                        }
                    },
                    fetch_item(slug, qty, sale_id) {
                        const vm = this;
                        {
                            if (slug) {
                                axios.get(this.config.get_product_info_url + '/' + slug, {
                                    params: {
                                        store_id: vm.store_id,
                                        sale_id: sale_id
                                    }
                                }).then(function (response) {
                                    const product_details = response.data;
                                    vm.items.push({
                                        item_id: slug,
                                        group: product_details.group,
                                        product_name: product_details.name,
                                        unit: product_details.unit,
                                        stock: product_details.balance_qty,
                                        price: product_details.price,
                                        sale_price: product_details.price,
                                        quantity: qty,
                                        product_discount: product_details.product_discount,
                                        subtotal: 0,
                                    });
                                    // vm.item_id = '';
                                    // vm.category_id = '';

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
                        //
                        // if (index.quantity > index.stock) {
                        //     index.quantity = index.stock;
                        // }
                        // if (index.quantity <= 0) {
                        //     index.quantity = '';
                        // }
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
                    getStoreData: function (afterLoad) {
                        const vm = this
                        if (!vm.store_id) {
                            toastr.error('Please Select valid Store', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return;
                        }
                        axios.get('/search-sales/', {
                            params: {
                                store_id: vm.store_id
                            }
                        }).then((response) => {
                            vm.salesData = response.data || []
                            if (vm.sale_id && vm.prefillSale) {
                                var exists = vm.salesData.some(function (row) {
                                    return String(row.id) === String(vm.sale_id);
                                });
                                if (!exists) {
                                    vm.salesData.unshift(vm.prefillSale);
                                }
                            }
                            vm.$nextTick(function () {
                                if (vm.sale_id) {
                                    $('.select2').val(vm.sale_id).trigger('change.select2');
                                }
                                if (typeof afterLoad === 'function') {
                                    afterLoad();
                                }
                            });
                        }).catch(function () {
                            if (typeof afterLoad === 'function') {
                                afterLoad();
                            }
                        });
                    },
                    bootstrapPrefill: function () {
                        var vm = this;
                        if (!vm.store_id) {
                            toastr.warning('Could not resolve store for this delivery.', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return;
                        }
                        vm.getStoreData(function () {
                            vm.getAllData();
                        });
                    },
                    deletePaymentMethod: function (row) {
                        this.paymentMethods.splice(this.paymentMethods.indexOf(row), 1);
                    },
                    addMorePaymentMethod() {
                        this.paymentMethods.push({amount: 0, method: ''})
                    },
                    setStoreId: function () {
                        var vm = this;
                        if (!vm.store_id && "{{ $user_store ? $user_store->id : '' }}") {
                            vm.store_id = "{{ $user_store ? $user_store->id : '' }}";
                            vm.getStoreData();
                        }
                    },
                    preventEnterSubmit() {
                        const vm = this;
                        document.querySelector('form').addEventListener('keypress', function (e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                return false;
                            }
                        });
                    },
                    // Handle form submission on button click
                    submitForm() {
                        const vm = this;
                        if (vm.isSubmitting) return;
                        vm.isSubmitting = true;

                        if (vm.items.length === 0) {
                            toastr.error('Please add items before submitting', {
                                closeButton: true,
                                progressBar: true,
                            });
                            vm.isSubmitting = false;
                            return;
                        }

                        document.getElementById('deliveryForm').submit(); // Use ID to be 100% sure
                    }
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
