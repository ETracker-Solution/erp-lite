@extends('layouts.app')
@section('title')
    Sales Return
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('vue-js/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <style>
        .list-hover:hover {
            background-color: #f1f1f1;

        }
    </style>
@endsection

@section('css')
    <style>
        .green {
            background-color: #008000;
        }

        .yellow {
            background-color: #FFFF00;
        }

        table tr td {
            color: #000;
        }

        #li_hover ul li:hover {
            background-color: #008000;
            color: #fff;
        }
    </style>

@endsection

@section('content')
    <!-- Content Header (Page header) -->

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-1">

            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title" style="color:#115548;">Sales Return Entry</h3>
                            <div class="card-tools">
                                <a href="{{route('sales-returns.index')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-list"
                                                                              aria-hidden="true"></i> &nbsp;See List
                                    </button>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <!-- end page title -->
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <form action="{{ route('sales-returns.store') }}" method="post">
                                        @csrf
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-box" id="vue_app">
                                                    <div class="row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                             id="li_hover">
                                                            <div>
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <div class="form-group" style="position: relative">
                                                                            <div class="input-group">
                                                                                <div class="input-group-prepend">
                                                                            <span class="input-group-text"
                                                                                  id="basic-addon1"><i
                                                                                    class="fa fa-barcode"
                                                                                    aria-hidden="true"></i></span>
                                                                                </div>
                                                                                <input type="text"
                                                                                       placeholder="Search Invoice"
                                                                                       v-model="searchquery"
                                                                                       v-on:keyup="autoComplete"
                                                                                       class="form-control input-sm" autofocus>

                                                                            </div>
                                                                            <ul class="list-group"
                                                                                style="position: absolute; width:100% !important;z-index:2;">

                                                                                <li style="cursor: pointer;"
                                                                                    class="list-group-item list-hover"
                                                                                    v-for="result in data_results"
                                                                                    v-on:click="selectautoCompleteInvoice(result.id)"
                                                                                    sale_id="result.id">
                                                                                    @{{ result.invoice_number }}
                                                                                </li>

                                                                            </ul>


                                                                        </div>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <div class="form-group">
                                                                            <label for="">Select Store</label>
                                                                            <select name="store_id" id="" class="form-control-sm" required>
                                                                                <option value="">Choose Store</option>
                                                                                @foreach($stores as $store)
                                                                                    <option value="{{ $store->id }}">{{ $store->id }} - {{ $store->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead class="bg-secondary">
                                                                    <tr>
                                                                        <th style="width: 5%">#</th>
                                                                        <th style="width: 15%">Group</th>
                                                                        <th style="width:25%">Item</th>
                                                                        <th style="width: 5%">Unit</th>
                                                                        <th style="width: 8%">Selling Price</th>
                                                                        <th style="width: 15%">Discount</th>
                                                                        <th style="width: 10%;vertical-align: middle">
                                                                            Sale Quantity
                                                                        </th>
                                                                        <th style="width: 10%;vertical-align: middle">
                                                                            Return
                                                                            Quantity
                                                                        </th>
                                                                        <th style="width: 10%;vertical-align: middle">
                                                                            Item total
                                                                        </th>
                                                                        <th style="width: 5%"></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <tr v-for="(row, index) in items">
                                                                        <td>
                                                                            @{{ ++index }}
                                                                        </td>
                                                                        <td style="vertical-align: middle">
                                                                            @{{ row.group }}
                                                                        </td>
                                                                        <td style="vertical-align: middle">
                                                                            <input type="hidden"
                                                                                   :name="'products['+index+'][coi_id]'"
                                                                                   class="form-control input-sm"
                                                                                   v-bind:value="row.coi_id">
                                                                            @{{ row.name }}
                                                                        </td>
                                                                        <td style="vertical-align: middle">
                                                                            @{{ row.unit }}
                                                                        </td>
                                                                        <td style="vertical-align: middle"
                                                                            class="text-right">
                                                                            <input type="number"
                                                                                   v-model="row.rate"
                                                                                   :name="'products['+index+'][rate]'"
                                                                                   class="form-control input-sm"
                                                                                   required readonly>
                                                                        </td>

                                                                        <td style="vertical-align: middle"
                                                                            class="text-right">
                                                                            <div class="row">
                                                                                <div class="col-4">
                                                                                    <select name="" id=""
                                                                                            class="form-control form-control-sm"
                                                                                            v-model="row.discount_type"
                                                                                            readonly
                                                                                    >
                                                                                        <option value="f">tk</option>
                                                                                        <option value="p">%</option>
                                                                                    </select>
                                                                                </div>
                                                                                <input type="hidden" :name="'products['+index+'][discount_type]'" v-model="row.discount_type">
                                                                                <input type="hidden" :name="'products['+index+'][discount]'" v-model="row.discount_amount">
                                                                                <div class="col-8">
                                                                                    <input type="number"
                                                                                           v-model="row.discount_value"
                                                                                           :name="'products['+index+'][discount_value]'"
                                                                                           class="form-control input-sm form-control-sm"
                                                                                           @keyup="updateProductDiscount(row)"
                                                                                           readonly
                                                                                           required>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td style="vertical-align: middle"
                                                                            class="text-right">
                                                                            <input type="number"
                                                                                   v-model="row.sale_quantity"
                                                                                   :name="'products['+index+'][sale_quantity]'"
                                                                                   class="form-control input-sm"
                                                                                   required readonly>
                                                                        </td>

                                                                        <td style="vertical-align: middle"
                                                                            class="text-right">
                                                                            <input type="number"
                                                                                   v-model="row.return_quantity"
                                                                                   :name="'products['+index+'][quantity]'"
                                                                                   class="form-control input-sm"
                                                                                   required>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                   class="form-control input-sm"
                                                                                   v-bind:value="item_total(row)"
                                                                                   readonly>
                                                                        </td>
                                                                        <td style="vertical-align: middle">
                                                                            <button type="button" class="btn btn-danger"
                                                                                    @click="delete_row(row)"><i
                                                                                    class="fa fa-trash"></i></button>
                                                                        </td>
                                                                    </tr>

                                                                    </tbody>
                                                                    <tfoot>
                                                                    <tr>
                                                                        <td colspan="10"
                                                                            style="background-color: #DDDCDC">

                                                                        </td>
                                                                    </tr>
{{--                                                                    <tr>--}}
{{--                                                                        <td colspan="8" class="text-right">--}}
{{--                                                                            Subtotal--}}
{{--                                                                        </td>--}}
{{--                                                                        <td class="text-right">--}}
{{--                                                                            @{{subtotal}}--}}
{{--                                                                            <input type="hidden"--}}
{{--                                                                                   :name="'subtotal'"--}}
{{--                                                                                   class="form-control input-sm"--}}
{{--                                                                                   v-bind:value="subtotal"--}}
{{--                                                                                   readonly>--}}
{{--                                                                            <input type="hidden" :name="'total_item'"--}}
{{--                                                                                   class="form-control input-sm"--}}
{{--                                                                                   v-bind:value="items.length" readonly>--}}
{{--                                                                        </td>--}}
{{--                                                                        <td></td>--}}
{{--                                                                    </tr>--}}
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <table class="table table-bordered small table-sm">
                                                                <thead  class="thead-light">
                                                                <tr>
                                                                    <th>Product Wise Discount</th>
                                                                    <th>Coupon Discount</th>
                                                                    <th>Overall Discount</th>
                                                                    <th>Special Discount</th>
                                                                    <th>Membership Discount</th>
                                                                    <th>Total Discount</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr>
                                                                    <td>@{{ productWiseDiscount }}</td>
                                                                    <td>@{{ selectedCouponCodeDiscountAmount }}</td>
                                                                    <td>@{{ selectedTotalDiscountAmount }}</td>
                                                                    <td>@{{ selectedSpecialDiscountAmount }}</td>
                                                                    <td>@{{ selectedSpecialDiscountAmount }}</td>
                                                                    <td>@{{ allDiscountAmount }}</td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <h3 class="text-center"> Exchange Amount: @{{
                                                                exchange_amount }}</h3>
                                                        </div>
                                                    </div>
                                                    <!--HiddenInput-->
                                                    <input type="hidden" name="sale_id" v-model="sale_data.id">
                                                    <input type="hidden" name="subtotal" v-model="subtotal">
                                                    <input type="hidden" name="discount" v-model="allDiscountAmount">
                                                    <input type="hidden" name="grand_total" v-model="exchange_amount">
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right"
                                                     v-if="items.length > 0">
                                                    <button class="float-right btn btn-primary" type="submit"><i
                                                            class="fa fa-fw fa-lg fa-check-circle"></i>Submit
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div> <!-- end col -->
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                </div>
            </div>
            <!-- /.row -->

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@push('script')
    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script src="{{ asset('vue-js/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>

    <script>
        $(document).ready(function () {

            var app = new Vue({
                el: '#vue_app',
                data: {
                    config: {
                        get_product_info_url: "{{ url('fetch-sale-info') }}",
                    },
                    searchquery: '',
                    data_results: [],
                    product_id: '',
                    items: [],
                    sale_data: {},
                },
                computed: {
                    subtotal: function () {
                        return this.items.reduce((total, item) => {
                            return total + (item.return_quantity * item.rate)
                        }, 0)
                    },
                    productWiseDiscount: function () {
                        return this.items.reduce((total, item) => {
                            return total + Number(item.discount_amount)
                        }, 0)
                    },
                    total_bill: function () {
                        return this.items.reduce((total, item) => {
                            return total + ((item.return_quantity * item.rate))
                        }, 0)
                    },
                    allDiscountAmount: function () {
                        var vm = this
                        return this.items.length > 0 ? Number(this.selectedTotalDiscountAmount) + Number(this.selectedSpecialDiscountAmount) + Number(this.selectedCouponCodeDiscountAmount) + Number(this.productWiseDiscount) + Number(this.selectedMembershipDiscountAmount) : 0
                    },
                    selectedTotalDiscountAmount: function () {
                        var vm = this
                        if (vm.items.length < 1) {
                            return 0;
                        }
                        if (vm.sale_data && vm.sale_data.total_discount_type === 'fixed') {
                            const saleItemCount = vm.sale_data.items.length
                            const perProductDiscount = vm.sale_data.total_discount_amount / saleItemCount
                            return this.items.reduce((total, item) => {
                                return total + (perProductDiscount)
                            }, 0)
                        }else if (vm.sale_data && vm.sale_data.total_discount_type === 'percentage') {
                            const discountPercentage = vm.sale_data.total_discount_value
                            return this.items.reduce((total, item) => {
                                return total + (((item.return_quantity * item.rate) * discountPercentage) / 100)
                            }, 0)
                        }else{
                            return 0
                        }
                    },
                    selectedSpecialDiscountAmount: function () {
                        var vm = this
                        if (vm.items.length < 1) {
                            return 0;
                        }
                        if (vm.sale_data && vm.sale_data.special_discount_amount > 0) {
                            const discountPercentage = vm.sale_data.special_discount_value
                            return this.items.reduce((total, item) => {
                                return total + (((item.return_quantity * item.rate) * discountPercentage) / 100)
                            }, 0)
                        }else{
                            return 0
                        }
                    },
                    selectedMembershipDiscountAmount: function () {
                        var vm = this
                        if (vm.items.length < 1) {
                            return 0;
                        }
                        if (vm.sale_data && vm.sale_data.membership_discount_amount > 0) {
                            const discountPercentage = vm.sale_data.membership_discount_percentage
                            return this.items.reduce((total, item) => {
                                return total + (((item.return_quantity * item.rate) * discountPercentage) / 100)
                            }, 0)
                        }else{
                            return 0;
                        }
                    },
                    selectedCouponCodeDiscountAmount: function () {
                        var vm = this
                        if (vm.items.length < 1) {
                            return 0;
                        }
                        if (vm.sale_data && vm.sale_data.couponCodeDiscountType === 'fixed') {
                            const saleItemCount = vm.sale_data.items.length
                            const perProductDiscount = vm.sale_data.minimumPurchaseAmount / saleItemCount
                            return this.items.reduce((total, item) => {
                                return total + (perProductDiscount)
                            }, 0)
                        }else if (vm.sale_data && vm.sale_data.couponCodeDiscountType === 'percentage') {
                            const discountPercentage = vm.sale_data.couponCodeDiscountValue
                            return this.items.reduce((total, item) => {
                                return total + (((item.return_quantity * item.rate) * discountPercentage) / 100)
                            }, 0)
                        }else{
                            return 0;
                        }
                    },
                    exchange_amount: function () {
                        return this.total_bill > 0 ? (this.total_bill - this.allDiscountAmount) : 0
                    }
                },
                methods: {
                    autoComplete() {
                        const vm = this;
                        vm.data_results = [];
                        if (vm.searchquery.length > 1) {
                            axios.get('/vuejs/autocomplete/sales-invoice-search', {
                                params: {
                                    searchquery: vm.searchquery
                                }
                            }).then(response => {
                                vm.data_results = response.data;
                                console.log(vm.data_results);
                            });
                        }
                    },
                    selectautoCompleteInvoice(sale_id) {
                        const vm = this;
                        if (!sale_id) {
                            toastr.error('Enter Invoice', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        } else {
                            var slug = sale_id;
                            if (slug) {
                                vm.items = []
                                axios.get(this.config.get_product_info_url + '/' + slug).then(function (response) {
                                    let item = response.data.items;
                                    for (key in item) {
                                        vm.items.push(item[key]);
                                    }
                                    vm.sale_data = response.data.sale
                                    vm.searchquery = '';
                                    vm.data_results = [];
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
                    item_total: function (index) {
                        if (index.sale_quantity < index.return_quantity) {
                            index.return_quantity = index.sale_quantity
                        }
                        var total_cost = (index.return_quantity * index.rate);
                        if (index.discount_type == 'p') {
                            index.discount_amount = (total_cost * index.discount_value) / 100;
                        }
                        if (index.discount_type == 'f') {
                            index.discount_amount = index.discount_value;
                        }
                        return index.return_quantity > 0 ? (index.return_quantity * index.rate) - index.discount_amount : 0;

                    },
                },
                //-------------
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
