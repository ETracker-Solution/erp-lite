@extends('layouts.app')
@section('title')
    Purchase
@endsection


@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Purchase Create'=>''
        ]
    @endphp
    <x-breadcrumb title='Purchase' :links="$links"/>


    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                   <span v-if="categoryLoading" class="categoryLoader">
                            <img src="{{ asset('loading.gif') }}" alt="loading">
                        </span>
                <div class="col-lg-12 col-md-12">
                    <form action="{{ route('purchases.store') }}" method="POST" class="">
                        @csrf
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Goods Purchase Bill (GPB) Entry</h3>
                                <div class="card-tools">
                                    <a href="{{route('purchases.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-plus-circle"
                                           aria-hidden="true"></i> &nbsp;
                                        Purchase
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
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="purchase_id">Purchase No</label>
                                                            <input type="text" class="form-control input-sm"
                                                                   value="{{$serial_no}}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="reference_no">Reference No</label>
                                                            <input type="text" class="form-control input-sm"
                                                                   value="{{old('reference_no')}}" name="reference_no">
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="supplier_id">Supplier Group</label>
                                                            <select name="supplier_group_id" id="supplier_group_id"
                                                                    class="form-control bSelect"
                                                                    v-model="supplier_group_id" required>
                                                                <option value="">Select Supplier Group</option>
                                                                @foreach($supplier_groups as $row)
                                                                    <option
                                                                        value="{{ $row->id }}">{{ $row->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="supplier_id">Supplier</label>
                                                            <select name="supplier_id" id="supplier_id"
                                                                    class="form-control bSelect" v-model="supplier_id"
                                                                    required>
                                                                <option value="">Select Supplier</option>
                                                                @foreach($suppliers as $supplier)
                                                                    <option
                                                                        value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="remark">Remark</label>
                                                            <textarea class="form-control" name="remark" rows="5"
                                                                      placeholder="Enter Remark"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Goods Purchase Line Item</h3>
                            </div>
                            <div class="card-body">

                                <div class="card-box">
                                    <div id="">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label for="group_id" class="control-label">Group</label>
                                                    <select class="form-control" name="group_id" v-model="group_id"
                                                            @change="fetch_product">
                                                        <option value="">Select One</option>
                                                        @foreach ($groups as $row)
                                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
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

                                                        <option :value="row.id" v-for="row in items"
                                                                v-html="row.name">
                                                        </option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="margin-top: 30px;">
                                                <button type="button" class="btn btn-info btn-block"
                                                        @click="data_input">Add
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
                                                 v-if="selected_items.length>0">

                                                <hr>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead class="bg-secondary">
                                                        <tr>
                                                            <th>Group</th>
                                                            <th>Item</th>
                                                            <th>Qty</th>
                                                            <th>Rate</th>
                                                            <th>Value</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(row, index) in selected_items">

                                                            <td>
                                                                @{{ row.group }}
                                                            </td>
                                                            <td>
                                                                @{{ row.name }}
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][coi_id]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.id">

                                                            </td>
                                                            <td>
                                                                <input type="number" v-model="row.quantity"
                                                                       :name="'products['+index+'][quantity]'"
                                                                       class="form-control input-sm"
                                                                       @change="itemtotal(row)" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" v-model="row.rate"
                                                                       :name="'products['+index+'][rate]'"
                                                                       class="form-control input-sm"
                                                                       @change="itemtotal(row)" required>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control input-sm"
                                                                       v-bind:value="itemtotal(row)" readonly>
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
                                                            <td colspan="3">

                                                            </td>
                                                            <td>
                                                                Subtotal
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control input-sm"
                                                                       name="subtotal" v-bind:value="subtotal" readonly>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">

                                                            </td>
                                                            <td>
                                                                Vat
                                                            </td>
                                                            <td>
                                                                <input type="text" name="vat"
                                                                       class="form-control input-sm" v-model="vat">
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">

                                                            </td>
                                                            <td>
                                                                Net Payable
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control input-sm"
                                                                       name="net_payable" v-bind:value="net_payable"
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
        .categoryLoader {
            position: absolute;
            top: 50%;
            right: 40%;
            transform: translate(-50%, -50%);
            color: red;
            z-index: 999;
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

    <script>
        $(document).ready(function () {

            var vue = new Vue({
                el: '#vue_app',
                data: {
                    config: {

                        get_items_info_by_group_id_url: "{{ url('fetch-items-by-group-id') }}",
                        get_item_info_url: "{{ url('fetch-item-info') }}",
                    },
                    vat: 0,
                    supplier_group_id: '',
                    supplier_id: '',
                    group_id: '',
                    item_id: '',
                    items: [],
                    selected_items: [],
                    categoryLoading: false
                },
                computed: {

                    subtotal: function () {
                        return this.selected_items.reduce((total, item) => {
                            return total + item.quantity * item.rate
                        }, 0)
                    },
                    net_payable: function () {
                        return this.subtotal + parseFloat(this.vat)
                    },
                },
                methods: {

                    fetch_product() {

                        var vm = this;

                        var slug = vm.group_id;

                        if (slug) {
                            vm.categoryLoading = true;
                            axios.get(this.config.get_items_info_by_group_id_url + '/' + slug).then(function (response) {

                                // vm.selected_items = response.data.products;
                                vm.items = response.data.products;
                                vm.categoryLoading = false;
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
                            toastr.error('Please Select Item', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        } else {

                            var slug = vm.item_id;

                            if (slug) {
                                vm.categoryLoading = true;
                                axios.get(this.config.get_item_info_url + '/' + slug).then(function (response) {
                                    let item_info = response.data;
                                    console.log(item_info);
                                    vm.selected_items.push({
                                        id: item_info.id,
                                        group: item_info.name,
                                        name: item_info.name,
                                        rate: '',
                                        quantity: '',
                                    });
                                    console.log(vm.selected_items);
                                    vm.item_id = '';
                                    vm.group_id = '';
                                    vm.categoryLoading = false;

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
                    itemtotal: function (index) {

                        console.log(index.quantity * index.rate);
                        return index.quantity * index.rate;


                        //   alert(quantity);
                        //  var total= row.quantity);
                        //  row.itemtotal=total;
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
