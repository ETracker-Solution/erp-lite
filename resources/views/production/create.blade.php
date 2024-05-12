@extends('layouts.app')
@section('title', 'FG Production')
@section('content')
    @php
        $links = [
        'Home' => route('dashboard'),
        'FG Production' => route('productions.index'),
        'FG Production create' => '',
        ];
    @endphp
    <x-breadcrumb title='FG Production' :links="$links"/>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                   <span v-if="pageLoading" class="pageLoader">
                            <img src="{{ asset('loading.gif') }}" alt="loading">
                        </span>
                <div class="col-lg-12 col-md-12">
                    <form action="{{ route('productions.store') }}" method="POST" class="">
                        @csrf
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">FG Production (FGP) Entry</h3>
                                <div class="card-tools">
                                    <a href="{{route('productions.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-bars"
                                           aria-hidden="true"></i> &nbsp;
                                        FG Production List
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
                                                            <label for="purchase_id">FGP No</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control input-sm"
                                                                       value="{{$serial_no}}" name="serial_no"
                                                                       id="serial_no"  v-model="serial_no">
                                                                <span class="input-group-append">
                                                                    <button type="button" class="btn btn-info btn-flat"  @click="data_edit">Search</button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="date">Date</label>
                                                            <vuejs-datepicker v-model="date" name="date"
                                                                              placeholder="Select date"
                                                                              format="yyyy-MM-dd"></vuejs-datepicker>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="row">

                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="batch_id">Batch</label>
                                                            <select name="batch_id" id="batch_id"
                                                                    class="form-control bSelect"
                                                                    v-model="batch_id" required>
                                                                <option value="">Select Batch</option>
                                                                @foreach($batches as $row)
                                                                    <option
                                                                        value="{{ $row->id }}">{{ $row->batch_no }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="store_id">Production Unit</label>
                                                            <select name="rm_store_id" id="rm_store_id"
                                                                    class="form-control bSelect"
                                                                    v-model="rm_store_id" required>
                                                                <option value="">Select One</option>
                                                                @foreach($rm_stores as $row)
                                                                    <option
                                                                        value="{{ $row->id }}">{{ $row->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div> <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="store_id">Store</label>
                                                            <select name="store_id" id="store_id"
                                                                    class="form-control bSelect"
                                                                    v-model="store_id" required>
                                                                <option value="">Select One</option>
                                                                @foreach($fg_stores as $row)
                                                                    <option
                                                                        value="{{ $row->id }}">{{ $row->name }}</option>
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
                                                            <label for="reference_no">Reference No</label>
                                                            <input type="text" class="form-control input-sm"
                                                                   value="{{old('reference_no')}}" name="reference_no">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
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

                            </div>

                        </div>
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">FG Production (FGP) Line Item</h3>
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
                                                            </td> <td>
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
                                                            <td colspan="2">
                                                                Total
                                                            </td>

                                                            <td>
                                                                @{{ total_quantity }}
                                                                <input type="hidden" class="form-control input-sm"
                                                                       name="total_quantity" v-bind:value="total_quantity" readonly>
                                                            </td>
                                                            <td></td>
                                                            <td>
                                                                @{{ subtotal }}
                                                                <input type="hidden" class="form-control input-sm"
                                                                       name="subtotal" v-bind:value="subtotal" readonly>
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

                        get_items_info_by_group_id_url: "{{ url('fetch-items-by-group-id') }}",
                        get_item_info_url: "{{ url('fetch-item-info') }}",
                    },
                    date: new Date(),
                    serial_no: {{$serial_no}},
                    rm_store_id: '',
                    store_id: '',
                    batch_id: '',
                    group_id: '',
                    item_id: '',
                    items: [],
                    selected_items: [],
                    pageLoading: false
                },
                components: {
                    vuejsDatepicker
                },
                computed: {
                    total_quantity: function () {
                        return this.selected_items.reduce((total, item) => {
                            return total+ parseFloat(item.quantity)
                        }, 0)
                    },
                    subtotal: function () {
                        return this.selected_items.reduce((total, item) => {
                            return total + item.quantity * item.rate
                        }, 0)
                    },


                },
                methods: {

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
                                        id: item_info.id,
                                        group: item_info.parent.name,
                                        name: item_info.name,
                                        unit: item_info.unit.name,
                                        rate: item_info.price,
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

                    },

                    data_edit() {
                        var vm = this;

                        alert(vm.serial_no);
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
                                        id: item_info.id,
                                        group: item_info.parent.name,
                                        name: item_info.name,
                                        rate: '',
                                        quantity: 1,
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
                    valid_quantity: function (index) {
                        if (index.quantity <= 0) {
                            toastr.error('Quantity 0 or Negative not Allow', {
                                closeButton: true,
                                progressBar: true,
                            });
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
