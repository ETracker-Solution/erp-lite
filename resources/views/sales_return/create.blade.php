@extends('layouts.app')
@section('title')
    Sales Return
@endsection

@section('css')
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/libs/datatables/datatables.min.css')}}">
    <link rel="stylesheet" href="{{ asset('vue-js/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <style>
        .list-hover:hover {
            background-color: #f1f1f1;

        }
    </style>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('/') }}datatable/dataTables.bootstrap4.min.css">
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
                                    <form action="{{ route('sales-returns.store') }}" method="post" target="_blank">
                                        @csrf
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-box" id="vue_app">
                                                    <div class="row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                             id="li_hover">
                                                            <div>
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
                                                                                            v-model="row.discountType"
                                                                                    >
                                                                                        <option value="">tk</option>
                                                                                        <option value="">%</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-8">
                                                                                    <input type="number"
                                                                                           v-model="row.product_discount"
                                                                                           :name="'products['+index+'][product_discount]'"
                                                                                           class="form-control input-sm form-control-sm"
                                                                                           v-model="row.discountValue"
                                                                                           @keyup="updateProductDiscount(row)"
                                                                                           :disabled="!row.discountable"
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
                                                                                   @change="valid(row)" required>
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
                                                                    <tr>
                                                                        <td colspan="8" class="text-right">
                                                                            Subtotal
                                                                        </td>
                                                                        <td class="text-right">
                                                                            @{{subtotal}}
                                                                            <input type="hidden"
                                                                                   :name="'subtotal'"
                                                                                   class="form-control input-sm"
                                                                                   v-bind:value="subtotal"
                                                                                   readonly>
                                                                            <input type="hidden" :name="'total_item'"
                                                                                   class="form-control input-sm"
                                                                                   v-bind:value="items.length" readonly>
                                                                        </td>
                                                                        <td></td>
                                                                    </tr>
                                                                    </tfoot>
                                                                </table>
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

                },
                computed: {
                    subtotal: function () {
                        return this.items.reduce((total, item) => {
                            return total + (item.quantity * item.rate)
                        }, 0)
                    }
                },
                //--------------
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
                                axios.get(this.config.get_product_info_url + '/' + slug).then(function (response) {

                                    let item = response.data.items;
                                    for (key in item) {
                                        vm.items.push(item[key]);
                                    }
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

                        return (index.quantity * index.rate);

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
