@extends('layouts.app')
@section('title')
    FG Inventory Adjustment
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'FG Inventory Adjustment list'=>''
        ]
    @endphp
    <x-breadcrumb title='FG Inventory Adjustment Entry' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <span v-if="pageLoading" class="pageLoader">
                    <img src="{{ asset('loading.gif') }}" alt="loading">
                </span>
                <div class="col-lg-12 col-md-12">
                    <form action="{{ route('fg-inventory-adjustments.store') }}" method="POST" class="">
                        @csrf
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">FG Inventory Transfer(FGIT) Entry </h3>
                                <div class="card-tools">
                                    <a class="btn btn-sm btn-primary" href="{{route('fg-inventory-adjustments.index')}}">
                                            <i class="fa fa-list" aria-hidden="true"></i> &nbsp;FG Inventory Adjustment
                                            List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="card-box">
                                    <hr>
                                    <div id="">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="serial_no">FGIT No</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control input-sm"
                                                               value="{{$serial_no}}" name="serial_no"
                                                               id="serial_no">
                                                        <span class="input-group-append">
                                                                    <button type="button"
                                                                            class="btn btn-info btn-flat">Search</button>
                                                                </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="store_id">Store</label>
                                                    <select name="store_id" id="store_id"
                                                            class="form-control bSelect"
                                                            v-model="store_id" required>
                                                        <option value="">Select One</option>
                                                        @foreach($stores as $row)
                                                            <option
                                                                value="{{ $row->id }}">{{ $row->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="reference_no">Reference No</label>
                                                    <input type="text" class="form-control input-sm"
                                                           value="{{old('reference_no')}}" name="reference_no">
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="date">Date</label>
                                                    <vuejs-datepicker v-model="date" name="date"
                                                                      placeholder="Select date"
                                                                      format="yyyy-MM-dd"></vuejs-datepicker>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="remark">Remark</label>
                                                    <textarea class="form-control" name="remark" rows="1"
                                                              placeholder="Enter Remark"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">FGT Item Information</h3>
                                <div class="card-tools">

                                </div>
                            </div>

                            <div class="card-body">
                                <div class="card-box">
                                    <div id="">
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
                                                            <th width="20"></th>
                                                            <th>Group</th>
                                                            <th>Item</th>
                                                            <th>Unit</th>
                                                            <th>Balance Qty</th>
                                                            <th>Selling Price</th>
                                                            <th width="180">Quantity</th>
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
                                                                       :name="'products['+index+'][coi_id]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.coi_id">
                                                                @{{ row.name }}
                                                            </td>
                                                            <td>
                                                                @{{ row.unit }}
                                                            </td>
                                                            <td class="text-right">
                                                                @{{ row.balance_qty }}
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][balance_qty]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.balance_qty" readonly>
                                                            </td>
                                                            <td class="text-right">
                                                                @{{ row.price }}
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][rate]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.price" readonly>
                                                            </td>
                                                            <td class="text-right">
                                                                <input type="number" v-model="row.quantity"
                                                                       :name="'products['+index+'][quantity]'"
                                                                       class="form-control input-sm"
                                                                       @change="valid(row);item_total(row)" required>
                                                            </td>
                                                            <td class="text-right">
                                                                @{{ item_total(row) }}
                                                            </td>

                                                        </tr>

                                                        </tbody>
                                                        <tfoot>

                                                        <tr>
                                                            <td colspan="6">

                                                            </td>
                                                            <td class="text-right">
                                                                SubTotal
                                                            </td>
                                                            <td class="text-right">
                                                                @{{subtotal}}
                                                            </td>
                                                        </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right" v-if="items.length > 0">
                                    <button class="float-right btn btn-primary" type="submit"><i
                                            class="fa fa-fw fa-lg fa-check-circle"></i>Submit
                                    </button>
                                </div>
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
                        get_item_info_url: "{{ url('fetch-item-by-id-for-sale') }}",
                    },

                    date: new Date(),
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

                },
                components: {
                    vuejsDatepicker
                },
                computed: {

                    subtotal: function () {
                        return this.items.reduce((total, item) => {
                            return total + (item.quantity * item.price)
                        }, 0)
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
                                axios.get(this.config.get_item_info_url + '/' + slug).then(function (response) {

                                    product_details = response.data;
                                    vm.items.push({
                                        coi_id: product_details.coi_id,
                                        group: product_details.group,
                                        name: product_details.name,
                                        unit: product_details.unit,
                                        balance_qty: product_details.balance_qty,
                                        price: product_details.price,
                                        quantity: '',
                                        item_total: 0,
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
                    item_total: function (index) {

                        //   console.log(index.quantity * index.price);
                        return (index.quantity * index.price);


                        //   alert(quantity);
                        //  var total= row.quantity);
                        //  row.item_total=total;
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
