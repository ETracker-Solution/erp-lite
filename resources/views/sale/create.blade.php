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
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="customer_id">Customer Number</label>
                                                    <input type="text" class="form-control" placeholder="Enter Customer Number" v-model="customerNumber"
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
                                                    <input type="text" v-model="customer.name" class="form-control" disabled>
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
{{--                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <label for="remark">Remark</label>--}}
{{--                                                    <textarea class="form-control" name="remark" rows="1"--}}
{{--                                                              placeholder="Enter Remark"></textarea>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
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
                                                                       v-bind:value="subtotal" readonly>

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
                                                                       class="form-control input-sm" v-model="discount">

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
                                                                       v-bind:value="grandtotal" readonly>

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7">

                                                            </td>
                                                            <td>
                                                                Receive Amount
                                                            </td>
                                                            <td>
                                                                <input type="text" name="receive_amount"
                                                                       class="form-control input-sm"
                                                                       v-model="receive_amount">

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7">
                                                            </td>
                                                            <td>
                                                                Change Amount
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control input-sm"
                                                                       name="change_amount" v-bind:value="change_amount"
                                                                       readonly>

                                                            </td>
                                                        </tr>
                                                        <tfoot>
                                                    </table>
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
@section('css')
    <link rel="stylesheet" type="text/css"
          href="{{ asset('vue-js/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endsection
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
                    },
                    customer_id: '',
                    store_id: '',
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

                },
                methods: {

                    fetch_item() {

                        var vm = this;

                        var slug = vm.category_id;
                        //    alert(slug);
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

                        //   console.log(index.quantity * index.price);
                        return (index.quantity * index.price) - index.product_discount;


                        //   alert(quantity);
                        //  var total= row.quantity);
                        //  row.itemtotal=total;
                    },
                    valid: function (index) {
                        console.log(index.stock);
                        console.log(index.quantity);

                        if (index.quantity > index.stock) {
                            //console.log('2');
                            index.quantity = index.stock;
                        }
                        if (index.quantity <= 0) {
                            //console.log('3');
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
                            if(vm.customer.name){
                                vm.customer.name = vm.customer.name + '  (' + vm.customer.current_point+ '  point)'
                            }
                        }).catch(function (error) {
                            toastr.error(error, {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });
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
