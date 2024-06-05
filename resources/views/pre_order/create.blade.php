@extends('layouts.app')
@section('title', 'Pre Order')

@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Pre Order Entry'=>''
        ]
    @endphp
    <x-breadcrumb title='Pre Order' :links="$links"/>


    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                   <span v-if="pageLoading" class="pageLoader">
                            <img src="{{ asset('loading.gif') }}" alt="loading">
                        </span>
                <div class="col-lg-12 col-md-12">
                    <form action="{{ route('pre-orders.store') }}" method="POST" class="" enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Pre Order(PO) Entry</h3>
                                <div class="card-tools">
                                    <a href="{{route('pre-orders.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-bars"
                                           aria-hidden="true"></i> &nbsp;
                                        Pre Order List
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="card-box">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="uid">Order Number</label>
                                                <input type="text" class="form-control input-sm"
                                                       value="{{$uid}}" name="uid"
                                                       id="uid" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="date">Delivery Date <span class="text-danger">*</span></label>
                                                <vuejs-datepicker v-model="date" name="date"
                                                                  placeholder="Select date"
                                                                  format="yyyy-MM-dd"></vuejs-datepicker>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="outlet_id">Outlet<span class="text-danger">*</span></label>
                                                <select name="outlet_id" id="outlet_id"
                                                        class="form-control bSelect" required>
                                                    <option value="">Select One</option>
                                                    @foreach($outlets as $row)
                                                        <option
                                                            value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="customer_id">Customer <span class="text-danger">*</span></label>
                                                <select name="customer_id" id="customer_id"
                                                        class="form-control bSelect"
                                                        v-model="customer_id"
                                                        @change="fetch_supplier" required>
                                                    <option value="">Select One</option>
                                                    @foreach($customers as $row)
                                                        <option
                                                            value="{{ $row->id }}">{{ $row->name }}
                                                            -{{ $row->mobile }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                            <div class="form-group">
                                                <label for="remark">Description <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="remark" rows="2"
                                                          placeholder="Enter Description" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <div class="form-group">
                                                <label for="image">Attachment</label>
                                                <input type="file" class="form-control" name="image">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Pre Order Line Item</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="group_id" class="control-label">Group</label>
                                            <select class="form-control bSelect" name="group_id"
                                                    v-model="group_id"
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
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12"
                                         style="margin-top: 30px;">
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
                                                    <th style="width: 10px">#</th>
                                                    <th style="width: 200px">Group</th>
                                                    <th>Item</th>
                                                    <th style="width: 50px">Unit</th>
                                                    <th style="width: 180px">Qty</th>
                                                    <th style="width: 180px">Selling Price</th>
                                                    <th style="width: 180px">Discount</th>
                                                    <th style="width: 180px">Value</th>
                                                    <th style="width: 10px"></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(row, index) in selected_items">
                                                    <td style="vertical-align: middle">
                                                        @{{ ++index }}
                                                    </td>
                                                    <td style="vertical-align: middle">
                                                        @{{ row.group }}
                                                    </td>
                                                    <td style="vertical-align: middle">
                                                        @{{ row.name }}
                                                        <input type="hidden"
                                                               :name="'products['+index+'][coi_id]'"
                                                               class="form-control input-sm"
                                                               v-bind:value="row.id">

                                                    </td>
                                                    <td style="vertical-align: middle">
                                                        @{{ row.unit }}
                                                    </td>
                                                    <td>
                                                        <input type="text" v-model="row.quantity"
                                                               :name="'products['+index+'][quantity]'"
                                                               class="form-control input-sm text-right"
                                                               placeholder="0.00"
                                                               @change="itemtotal(row);valid_quantity(row)"
                                                               required>
                                                    </td>

                                                    <td>
                                                        <input type="text" v-model="row.rate"
                                                               :name="'products['+index+'][unit_price]'"
                                                               v-bind:readonly="row.isReadonly"
                                                               class="form-control input-sm text-right"
                                                               placeholder="0.00"
                                                               @change="itemtotal(row);valid_rate(row)"
                                                               required>
                                                    </td>
                                                    <td>
                                                        <input type="text" v-model="row.discount"
                                                               :name="'products['+index+'][discount]'"
                                                               class="form-control input-sm text-right"
                                                               placeholder="0.00"
                                                               @change="itemtotal(row);valid(row)"
                                                               required>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control input-sm text-right"
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
                                                    <td colspan="9" style="background-color: #DDDCDC">

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6"></td>
                                                    <td style="vertical-align: middle">
                                                        Subtotal
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control input-sm text-right"
                                                               name="subtotal" v-bind:value="subtotal"
                                                               readonly>
                                                    </td>
                                                    <td></td>
                                                </tr>


                                                <tr>
                                                    <td colspan="6"></td>
                                                    <td style="vertical-align: middle">
                                                        Discount
                                                    </td>
                                                    <td>
                                                        <input type="text" name="discount"
                                                               class="form-control input-sm text-right" v-model="discount"
                                                               readonly>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6"></td>
                                                    <td style="vertical-align: middle">
                                                        Total Vat
                                                    </td>
                                                    <td>
                                                        <input type="text" name="vat"
                                                               class="form-control input-sm text-right" v-model="vat"
                                                               placeholder="0.00">
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6"></td>
                                                    <td style="vertical-align: middle">
                                                        Grand Total
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control input-sm text-right"
                                                               name="grand_total" v-bind:value="grand_total" readonly>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6"></td>
                                                    <td style="vertical-align: middle">
                                                        Advance Amount
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control input-sm text-right"
                                                               name="advance_amount" v-model="advance_amount"
                                                               placeholder="0.00">
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6"></td>
                                                    <td style="vertical-align: middle">
                                                        Due Amount
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control input-sm text-right"
                                                               name="due_amount" v-bind:value="due_amount"
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
    <style>
        .table td {
            padding: 2px;
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
    <script src="https://cms.diu.ac/vue/vuejs-datepicker.min.js"></script>
    <script>
        $(document).ready(function () {

            var vue = new Vue({
                el: '#vue_app',
                data: {
                    config: {

                        get_suppliers_info_by_group_id_url: "{{ url('fetch-suppliers-by-group-id') }}",
                        get_items_info_by_group_id_url: "{{ url('fetch-items-by-group-id') }}",
                        get_item_info_url: "{{ url('fetch-item-by-id-for-pre-order') }}",
                    },
                    date: new Date(),
                    advance_amount: null,
                    vat: null,
                    supplier_group_id: '',
                    supplier_id: '',
                    customer_id: '',
                    group_id: '',
                    item_id: '',
                    items: [],
                    suppliers: [],
                    selected_items: [],
                    pageLoading: false
                },
                components: {
                    vuejsDatepicker
                },
                computed: {

                    subtotal: function () {
                        return this.selected_items.reduce((total, item) => {
                            return total + item.quantity * item.rate
                        }, 0)
                    },
                    discount: function () {
                        return this.selected_items.reduce((total, item) => {
                            return total + (item.discount ? parseFloat(item.discount) : 0)
                        }, 0)
                    },
                    grand_total: function () {
                        return this.subtotal + (this.vat ? parseFloat(this.vat) : 0) - this.discount
                    },
                    due_amount: function () {
                        return this.grand_total - (this.advance_amount ? parseFloat(this.advance_amount) : 0)
                    }
                },
                methods: {

                    fetch_supplier() {

                        var vm = this;

                        var slug = vm.supplier_group_id;

                        if (slug) {
                            vm.pageLoading = true;
                            axios.get(this.config.get_suppliers_info_by_group_id_url + '/' + slug).then(function (response) {

                                // vm.selected_items = response.data.products;
                                vm.suppliers = response.data.suppliers;
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

                    fetch_product() {

                        var vm = this;

                        var slug = vm.group_id;

                        if (slug) {
                            vm.pageLoading = true;
                            axios.get(this.config.get_items_info_by_group_id_url + '/' + slug).then(function (response) {

                                // vm.selected_items = response.data.products;
                                vm.items = response.data.products;
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
                            toastr.error('Please Select Item', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        } else {

                            var slug = vm.item_id;

                            if (slug) {
                                vm.pageLoading = true;
                                axios.get(this.config.get_item_info_url + '/' + slug).then(function (response) {
                                    let item_info = response.data;
                                    console.log(item_info);
                                    vm.selected_items.push({
                                        id: item_info.coi_id,
                                        group: item_info.group,
                                        name: item_info.name,
                                        unit: item_info.unit,
                                        discount: item_info.discount,
                                        rate: item_info.rate,
                                        quantity: '',
                                        isReadonly: item_info.is_readonly,
                                    });
                                    console.log(vm.selected_items);
                                    // vm.item_id = '';
                                    // vm.group_id = '';
                                    vm.pageLoading = false;

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
                    },
                    valid_quantity: function (index) {
                        if (index.quantity <= 0) {
                            toastr.error('Quantity 0 or Negative not Allow', {
                                closeButton: true,
                                progressBar: true,
                            });
                            index.quantity = '';
                        }
                    },
                    valid_rate: function (index) {
                        if (index.rate <= 0) {
                            toastr.error('Rate 0 or Negative not Allow', {
                                closeButton: true,
                                progressBar: true,
                            });
                            index.rate = '';
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
