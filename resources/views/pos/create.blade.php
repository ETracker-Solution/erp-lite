@extends('layouts.app')
@section('title')
    POS
@endsection
@section('css')
    <style>
        .product {
            border: 1px solid #DDD;
            cursor: pointer;
        }

        .categoryLoader {
            position: absolute;
            top: 50%;
            right: 25%;
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
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{asset('assets')}}/plugins/daterangepicker/daterangepicker.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{asset('assets')}}/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{asset('assets')}}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{asset('assets')}}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <link type="text/css" rel="stylesheet" href="{{ asset('vue-js/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <style>
        .list-hover:hover {
            background-color: #f1f1f1;

        }
    </style>
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>POS</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">POS</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" id="vue_app">
        <div class="container-fluid">
            <form id="scan_code" action="{{ route('pos.store') }}" method="POST" style="position: relative">
                @csrf
                <div class="row">
                    <div class="col-md-5">
                        <div class="card card-body">

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-barcode"></i>
                                </span>
                                </div>
                                <input type="text" @keyup="fetch_item_barcode()" v-model="search_barcode" id="id_code" placeholder="Scan Barcode" name="code" autocomplete="off" class="form-control ui-autocomplete-input">
                            </div>
                            <ul class="list-group" style="position: absolute; width:100% !important;z-index:2;">

                                <li style="cursor: pointer;" class="list-group-item list-hover" @click="data_input(result.id)" v-for="(result, index) in barcode_results">
                                    <a>@{{ result.name }} (@{{ result.code }})</a>
                                </li>
                            </ul>

                            <div class="form-row mb-3" style="position: relative">
                                <div class="col-md-12">
                                    <input type="text" @keyup="fetch_item()" v-model="search_keyword" id="product_search" placeholder="Start to write product name..." name="p_name" autocomplete="off" class="form-control ui-autocomplete-input">
                                </div>
                                <ul class="list-group" style="position: absolute; width:100% !important;z-index:2;top:50px;">

                                    <li style="cursor: pointer;" class="list-group-item list-hover" @click="data_input(result.id)" v-for="(result, index) in results">
                                        <a>@{{ result.name }} (@{{ result.code }})</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Date -->
                            <div class="form-group">
                                {{-- <div class="" id="reservationdate" data-target-input="nearest">--}}
                                {{-- <input type="date" class="form-control" placeholder="D/M/Y">--}}

                                <vuejs-datepicker v-model="exam_date" placeholder="Select date"></vuejs-datepicker>

                                <!-- <div class="input-group-append">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div> -->
                                {{-- </div>--}}
                            </div>
                            <!-- /.form group -->
                            <div class="row">
                                <div class="col-9">
                                    <!-- /.form group -->
                                    <div class="form-group">
                                        <select class="form-control bSelect" style="width: 100%;">
                                            <option selected="selected" value="0">Walking Customer</option>
                                            {{-- <option value="10">a - dfd</option> --}}

                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <a href="{{ route('customers.create') }}">
                                        <button type="button" data-toggle="modal" data-target="#add-customer-modal" class="btn btn-primary btn-block">
                                            Add
                                        </button>
                                    </a>
                                </div>
                            </div>

                            <form action="" id="order-form" method="POST">
                                <div class="row">
                                    <div class="col-md-12 mt-4">
                                        <table class="table table-bordered">
                                            <thead class="bg-primary">
                                            <tr>
                                                <th>Name</th>
                                                <th>Qty</th>
                                                <th>Price</th>
                                                <th>Sub Total</th>
                                                <th><a href="#" id="clearList"><i class="fa fa-trash" style="color: white;"></i></a></th>
                                            </tr>
                                            </thead>
                                            <tbody id="tbody">
                                            <!-- <tr>
                                            <td>
                                                android - 3G 4G android - 3G 4G android - 3G 4G
                                                <input type="hidden" value="android" name="name[]" class="name"> <input type="hidden" value="37" name="product_id[]">
                                            </td>
                                            <td>
                                                <div class="">
                                                    <input class="form-control" type="number" name="qyt[]" value="1" class="qyt">
                                                </div>
                                            </td>
                                            <td><input type="text" value="1400.00" name="rate[]" class="form-control rate"></td>
                                            <td><input type="text" readonly="readonly" name="sub_total[]" value="1400.00" class="form-control sub_total"></td>
                                            <td><a href="#" data-value="0" class="remove-btn item-index"><i class="fa fa-trash"></i></a></td>
                                        </tr> -->

                                            <tr v-for="(row, index) in items">
                                                <td>
                                                    <input type="hidden" :name="'products['+index+'][product_id]'" class="form-control input-sm" v-bind:value="row.product_id">

                                                    @{{ row.product_name }}
                                                </td>

                                                <td>
                                                    <input type="number" :name="'products['+index+'][quantity]'" class="form-control input-sm" v-model="row.quantity" @change="valid(row)" required>
                                                </td>
                                                <td>
                                                    @{{ row.price }}
                                                    <input type="hidden" :name="'products['+index+'][sale_price]'" class="form-control input-sm" v-bind:value="row.price">
                                                </td>
                                                <td> @{{ row.price * row.quantity }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-xs" @click="delete_row(row)"><i class="fa fa-trash"></i></button>
                                                </td>
                                            </tr>
                                            </tbody>
                                            <tfoot class="bg-danger">
                                            <tr>
                                                <th colspan="2" class="text-center">Total Qty: <strong id="totalQyt">@{{
                                                        totalqty }}</strong></th>
                                                <th colspan="3" class="text-center">Total: <strong id="totalAmount">@{{
                                                        subtotal }}</strong> Tk
                                                    <input type="hidden" name="subtotal" class="form-control input-sm" v-bind:value="subtotal" readonly>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="2" class="text-center"><strong id="totalQyt"></strong></th>
                                                <th class="text-center">Discount: <strong id="totalAmount"></strong></th>
                                                <td colspan="2">
                                                    <input type="text" name="discount" class="form-control input-sm" v-model="discount" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th colspan="2" class="text-center"><strong id="totalQyt"></strong></th>
                                                <th class="text-center">Grand Total: <strong id="totalAmount"></strong></th>
                                                <td colspan="2">
                                                    <input type="text" name="grandtotal" class="form-control input-sm" v-bind:value="grandtotal" readonly>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th colspan="2" class="text-center"><strong id="totalQyt"></strong></th>
                                                <th class="text-center">Received Amount: <strong id="totalAmount"></strong></th>
                                                <td colspan="2">
                                                    <input type="text" name="receive_amount" class="form-control input-sm" v-model="receive_amount" autocomplete="off">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th colspan="2" class="text-center"><strong id="totalQyt"></strong></th>
                                                <th class="text-center">Change Amount: <strong id="totalAmount"></strong></th>
                                                <td colspan="2">
                                                    <input type="text" class="form-control input-sm" name="change_amount" v-bind:value="change_amount" readonly>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                        <div class="form-gorup text-center">


                                            <button type="submit" id="payment-btn" class="btn btn-primary">
                                                <i class="fa fa-money" aria-hidden="true"></i>
                                                Submit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.col (left) -->
                    <div class="col-md-7" style="position: relative">
                        <div class="card card-default">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- /.form group -->
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select class="form-control bSelect" v-model="category_id" style="width: 100%;" v-on:change="fetch_sub_category_and_product">
                                                <option value="">Select one</option>

                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- <span v-if="categoryLoading">working....</span>--}}
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sub_category_id">Sub Category</label>
                                            <select name="sub_category_id" id="sub_category_id" class="form-control bSelect" v-model="sub_category_id" v-on:change="fetch_product">
                                                <option value="">Select one</option>
                                                <option :value="row.id" v-for="row in sub_categories" v-html="row.name">
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class=" product text-center col-md-3 col-sm-4 product" data-value="" v-for="row in products" :product_id="row.id" @click="data_input(row.id)">
                                        <img :src="'/storage/product/' + row.img_url" class="align-self-start img-thumbnail" :alt="row.name" style="width:80px;height:80px;">
                                        <br>
                                        <span v-html="row.name"></span>
                                        <br>
                                        <small class="font-weight-bold" v-html="row.selling_price">140.00</small> Tk
                                        <br>
                                        <small class="stock">Stock : 0</small>
                                    </div>
                                </div>

                                <span v-if="categoryLoading" class="categoryLoader">
                                <img src="{{ asset('loading.gif') }}" alt="loadign">
                            </span>
                                <!-- <nav class="my-3">
                                    <ul class="pagination justify-content-center">

                                        <li class="page-item disabled" aria-disabled="true" aria-label="« Previous">
                                            <span class="page-link" aria-hidden="true">‹</span>
                                        </li>
                                        <li class="page-item active" aria-current="page"><span class="page-link">1</span></li>
                                        <li class="page-item"><a class="page-link" href="">2</a></li>
                                        <li class="page-item"><a class="page-link" href="">3</a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="" rel="next" aria-label="Next »">›</a>
                                        </li>
                                    </ul>
                                </nav> -->

                            </div>
                            <div class="card-footer">

                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>

                    <!-- /.col (right) -->
                </div>
            </form>
            <!-- /.row -->

        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@endsection
@section('js')
    <!-- Select2 -->
    <script src="{{asset('admin')}}/plugins/select2/js/select2.full.min.js"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="{{asset('admin')}}/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <!-- InputMask -->
    <script src="{{asset('admin')}}/plugins/moment/moment.min.js"></script>
    <script src="{{asset('admin')}}/plugins/inputmask/jquery.inputmask.min.js"></script>
    <!-- date-range-picker -->
    <script src="{{asset('admin')}}/plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap color picker -->
    <script src="{{asset('admin')}}/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{asset('admin')}}/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Bootstrap Switch -->
    <script src="{{asset('admin')}}/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <!-- Page specific script -->
    <script>
        $(document).on('click', '#payment-btn', function() {
            // if ($.trim($('.name').val()) == '') {
            //     toastr.warning('Add Some Products...');
            //     return;
            // }

            $("#payment-modal").modal('show');
        });
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()
            $('#reservation').daterangepicker()

        })
    </script>
@endsection
@push('script')
    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script src="{{ asset('vue-js/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="https://cms.diu.ac/vue/vuejs-datepicker.min.js"></script>
    <script>
        $(document).ready(function() {

            var vue = new Vue({
                el: '#vue_app',
                data: {
                    config: {


                        get_keyword_wise_item_url: "{{ url('fetch-item-by-search-keyword') }}",

                        get_category_wise_sub_category_product_url: "{{ url('fetch-sub-category-product-info') }}",
                        get_sub_category_wise_product_url: "{{ url('fetch-sub-category-wise-product-info') }}",
                        get_product_info_url: "{{ url('fetch-product-info') }}",
                        get_all_product_url: "{{ url('fetch-all-product') }}",
                    },

                    exam_date: new Date(2021, 2, 20),
                    search_barcode: '',
                    barcode_results: [],
                    search_keyword: '',
                    results: [],

                    category_id: '',
                    sub_category_id: '',
                    sub_categories: [],
                    product_id: '',
                    products: [],
                    items: [],

                    quantity: 0,
                    price: 0,
                    discount: 0,
                    receive_amount: 0,
                    selling_price: 0,
                    categoryLoading: false

                },

                components: {
                    vuejsDatepicker
                },

                computed: {

                    subtotal: function() {
                        return this.items.reduce((total, item) => {
                            return total + item.quantity * item.price
                        }, 0)
                    },
                    totalqty: function() {
                        return this.items.reduce((qty, item) => {
                            return parseInt(qty) + parseInt(item.quantity)
                        }, 0)
                    },
                    grandtotal: function() {
                        return this.subtotal - this.discount
                    },
                    change_amount: function() {
                        return this.grandtotal - this.receive_amount
                    },

                },
                methods: {
                    async fetch_sub_category_and_product() {
                        var vm = this;
                        var slug = vm.category_id;
                        vm.categoryLoading = true;
                        if (slug) {
                            await axios.get(this.config.get_category_wise_sub_category_product_url + '/' + slug).then(function(response) {

                                vm.sub_categories = response.data.subCategory;
                                vm.products = response.data.products;

                            }).catch(function(error) {
                                toastr.error('Something went to wrong', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            }).finally(() => {
                                vm.categoryLoading = false;
                            });
                        }
                    },
                    fetch_product() {
                        var vm = this;
                        var catId = vm.category_id;
                        var subCatId = vm.sub_category_id;
                        if (subCatId) {
                            axios.get(this.config.get_sub_category_wise_product_url + '/' + catId + '/' + subCatId).then(function(response) {
                                vm.products = response.data.products;
                            }).catch(function(error) {
                                toastr.error('Something went to wrong', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return false;
                            });
                        }
                    },
                    fetch_all_product() {
                        var vm = this;
                        axios.get(this.config.get_all_product_url).then(function(response) {
                            vm.products = response.data.products;
                        }).catch(function(error) {
                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });

                    },
                    fetch_item() {
                        var vm = this;
                        var slug = vm.search_keyword;
                        // alert(slug);
                        if (slug) {
                            vm.results = [];
                            axios.get(this.config.get_keyword_wise_item_url + '/' + slug).then(function(response) {

                                vm.results = response.data;
                                console.log(vm.results);
                            }).catch(function(error) {

                                toastr.error('Something went to wrong', {
                                    closeButton: true,
                                    progressBar: true,
                                });

                                return false;

                            });
                        }

                    },
                    fetch_item_barcode() {
                        var vm = this;
                        var slug = vm.search_barcode;
                        // alert(slug);
                        if (slug) {
                            vm.results = [];
                            axios.get(this.config.get_keyword_wise_item_url + '/' + slug).then(function(response) {

                                vm.barcode_results = response.data;
                                console.log(vm.barcode_results);
                            }).catch(function(error) {

                                toastr.error('Something went to wrong', {
                                    closeButton: true,
                                    progressBar: true,
                                });

                                return false;

                            });
                        }

                    },
                    data_input(product_id) {
                        var vm = this;
                        if (!product_id) {
                            ;
                            toastr.error('Enter product', {
                                closeButton: true,
                                progressBar: true,
                            });

                            return false;

                        } else {

                            var slug = product_id;

                            if (slug) {
                                axios.get(this.config.get_product_info_url + '/' + slug).then(function(response) {

                                    product_details = response.data;
                                    console.log(product_details);
                                    vm.items.push({
                                        img_url: product_details.img_url,
                                        product_id: product_details.id,
                                        product_name: product_details.name,
                                        unit: product_details.unit,
                                        stock: product_details.stock,
                                        price: product_details.selling_price,
                                        quantity: 1,
                                        subtotal: 0,
                                    });

                                    vm.product_id = '';
                                    vm.category_id = '';
                                    vm.sub_category_id = '';
                                    vm.sub_categories = [];
                                    vm.results = [];
                                    vm.search_keyword = '';
                                    vm.barcode_results = [];
                                    vm.search_barcode = '';

                                }).catch(function(error) {

                                    toastr.error('Something went to wrong', {
                                        closeButton: true,
                                        progressBar: true,
                                    });

                                    return false;

                                });
                            }

                        }

                    },

                    delete_row: function(row) {
                        this.items.splice(this.items.indexOf(row), 1);
                    },
                    valid: function(index) {
                        //   console.log(index.quantity);
                        if (index.quantity <= 0) {
                            index.quantity = 1;
                        }
                    },
                    itemtotal: function(index) {

                        console.log(index.quantity * index.price);
                        return index.quantity * index.price;


                        //   alert(quantity);
                        //  var total= row.quantity);
                        //  row.itemtotal=total;
                    },

                },

                updated() {
                    $('.bSelect').selectpicker('refresh');
                },
                created() {
                    this.fetch_all_product();
                    // `this` points to the vm instance
                    console.log('count is: ') // => "count is: 1"
                }

            });

            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });

        });
    </script>
@endpush
