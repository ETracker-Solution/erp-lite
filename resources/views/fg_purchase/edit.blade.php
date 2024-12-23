@extends('layouts.app')
@section('title', 'Goods Purchase')

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
                   <span v-if="pageLoading" class="categoryLoader">
                            <img src="{{ asset('loading.gif') }}" alt="loading">
                        </span>
                <div class="col-lg-12 col-md-12">
                    <form action="{{route('fg-purchases.update',$purchase->id)}}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Goods Purchase Bill (GPB) Entry</h3>
                                <div class="card-tools">
                                    <a href="{{route('purchases.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-bars"
                                           aria-hidden="true"></i> &nbsp;
                                        Goods Purchase Bill List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="serial_no">Purchase No</label>
                                            <input type="text" class="form-control input-sm"
                                                   value="{{$purchase->uid}}" name="serial_no"
                                                   id="serial_no" readonly>
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
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="store_id">Store</label>
                                            <select name="store_id" id="store_id"
                                                    class="form-control bSelect" required>
                                                <option value="">Select Store</option>
                                                @foreach($stores as $row)
                                                    <option
                                                        value="{{ $row->id }}" {{ old('store_id',$purchase->store_id) == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="reference_no">Reference No</label>
                                            <input type="text" class="form-control input-sm"
                                                   value="{{old('reference_no',$purchase->reference_no)}}"
                                                   name="reference_no">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="supplier_id">Group</label>
                                            <select name="supplier_group_id" id="supplier_group_id"
                                                    class="form-control bSelect"
                                                    v-model="supplier_group_id"
                                                    @change="fetch_supplier">
                                                <option value="">Select Group</option>
                                                @foreach($supplier_groups as $row)
                                                    <option
                                                        value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="supplier_id">Supplier</label>
                                            <select name="supplier_id" id="supplier_id"
                                                    class="form-control bSelect" v-model="supplier_id"
                                                    required>
                                                <option value="">Select Supplier</option>
                                                <option :value="row.id" v-for="row in suppliers"
                                                        v-html="row.name">
                                                </option>

                                            </select>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <label for="remark">Remark</label>
                                            <textarea class="form-control" name="remark" rows="1"
                                                      placeholder="Enter Remark">{{$purchase->remark}}</textarea>
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
                                                            <th style="width: 10px">#</th>
                                                            <th style="width: 200px">Group</th>
                                                            <th>Item</th>
                                                            <th style="width: 50px">Unit</th>
                                                            <th style="width: 180px">Qty</th>
                                                            <th style="width: 180px">Rate</th>
                                                            <th style="width: 180px">Value</th>
                                                            <th style="width: 10px"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(row, index) in selected_items">

                                                            <td>
                                                                @{{ ++index }}
                                                            </td>
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
                                                                @{{ row.unit }}
                                                            </td>
                                                            <td>
                                                                <input type="number" v-model="row.quantity"
                                                                       :name="'products['+index+'][quantity]'"
                                                                       class="form-control input-sm"
                                                                       @change="itemtotal(row);valid_quantity(row)" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" v-model="row.rate"
                                                                       :name="'products['+index+'][rate]'"
                                                                       class="form-control input-sm"
                                                                       @change="itemtotal(row);valid_rate(row)" required>
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
                                                            <td colspan="8" style="background-color: #DDDCDC"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5">

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
                                                            <td colspan="5">

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
                                                            <td colspan="5">

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
                        get_item_info_url: "{{ url('fetch-item-info') }}",
                        get_old_items_data: "{{ url('fetch-purchase-by-id') }}",
                    },

                    purchase_id: '{{ $purchase->id }}',
                    date: '{{$purchase->date}}',
                    vat: {{$purchase->vat}},
                    supplier_group_id: {{$purchase->supplier->group->id}},
                    supplier_id: {{$purchase->supplier_id}},
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
                    net_payable: function () {
                        return this.subtotal + parseFloat(this.vat)
                    },
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

                            let slug = vm.item_id;
                            let exists = vm.selected_items.some(function (field) {
                                return field.id == slug
                            });
                            if (exists) {
                                toastr.info('Item Already Selected', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                            } else {
                                if (slug) {
                                    vm.pageLoading = true;
                                    axios.get(this.config.get_item_info_url + '/' + slug).then(function (response) {
                                        let item_info = response.data;
                                        console.log(item_info);
                                        vm.selected_items.push({
                                            id: item_info.id,
                                            unit: item_info.unit.name,
                                            group: item_info.parent.name,
                                            name: item_info.name,
                                            rate: '',
                                            quantity: '',
                                        });
                                        console.log(vm.selected_items);
                                        vm.item_id = '';
                                        vm.group_id = '';
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
                        }

                    },
                    delete_row: function (row) {
                        this.selected_items.splice(this.selected_items.indexOf(row), 1);
                    },
                    load_old() {
                        var vm = this;
                        var slug = vm.purchase_id;
                        //  alert(slug);
                        axios.get(this.config.get_old_items_data + '/' + slug).then(function (response) {
                            var item = response.data.items;
                            for (key in item) {
                                vm.selected_items.push(item[key]);
                            }
                        })
                    },
                    itemtotal: function (index) {

                        console.log(index.quantity * index.rate);
                        return index.quantity * index.rate;


                        //   alert(quantity);
                        //  var total= row.quantity);
                        //  row.itemtotal=total;
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
                beforeMount() {
                    this.load_old();
                    this.fetch_supplier();
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
