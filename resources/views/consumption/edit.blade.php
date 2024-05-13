@extends('layouts.app')
@section('title', 'Raw Material Consumption')
@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Raw Material Consumption' => route('consumptions.index'),
            'Raw Material Consumption create' => '',
        ]
    @endphp
    <x-breadcrumb title='Raw Material Consumption' :links="$links"/>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                   <span v-if="pageLoading" class="pageLoader">
                            <img src="{{ asset('loading.gif') }}" alt="loading">
                        </span>
                <div class="col-lg-12 col-md-12">
                    <form action="{{route('consumptions.update',$consumption->id)}}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Raw Material Consumption (RMC) Entry</h3>
                                <div class="card-tools">
                                    <a href="{{route('consumptions.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-bars"
                                           aria-hidden="true"></i> &nbsp;
                                        RM Consumption List
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
                                                            <label for="serial_no">RMC No</label>

                                                            <div class="input-group">
                                                                <input type="text" class="form-control input-sm"
                                                                       value="{{$serial_no}}" name="serial_no"
                                                                       id="serial_no" v-model="serial_no">
                                                                <span class="input-group-append">
                    <button type="button" class="btn btn-info btn-flat" @click="data_edit">Search</button>
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
                                                                        value="{{ $row->id }}">{{ $row->id }}
                                                                        -{{ $row->batch_no }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="store_id">Store</label>
                                                            <select name="store_id" id="store_id"
                                                                    class="form-control bSelect"
                                                                    v-model="store_id" required>
                                                                <option value="">Select Store</option>
                                                                @foreach($stores as $row)
                                                                    <option
                                                                        value="{{ $row->id }}">{{ $row->id }}
                                                                        -{{ $row->name }}</option>
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
                                                                   value="{{old('reference_no')}}" name="reference_no"
                                                                   v-model="reference_no">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="remark">Remark</label>
                                                            <textarea class="form-control" name="remark" rows="1"
                                                                      placeholder="Enter Remark"
                                                                      v-model="remark"></textarea>
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
                                <h3 class="card-title">Raw Material Consumption (RMC) Line Item</h3>
                            </div>
                            <div class="card-body">
                                <div class="card-box">
                                    <div id="">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
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
                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label for="item_id">Item</label>
                                                    <select name="item_id" id="item_id" class="form-control bSelect"
                                                            v-model="item_id" @change="fetch_item_balance">
                                                        <option value="">Select one</option>
                                                        <option :value="row.id" v-for="row in items"
                                                                v-html="row.name">
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-4 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="balance">Available Balance</label>
                                                    <input type="text" class="form-control input-sm"
                                                           :value="balance" name="balance" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12" style="margin-top: 30px;">
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
                                                            <th>#</th>
                                                            <th>Group</th>
                                                            <th>Item</th>
                                                            <th>Unit</th>
                                                            <th>Balance</th>
                                                            <th>Qty</th>
                                                            <th>Rate(Avg)</th>
                                                            <th>Value</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(row, index) in selected_items">

                                                            <td style="width: 10px">
                                                                @{{ ++index }}
                                                            </td>
                                                            <td style="width: 200px">
                                                                @{{ row.group }}
                                                            </td>
                                                            <td>
                                                                @{{ row.name }}
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][coi_id]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.id">

                                                            </td>
                                                            <td style="width: 50px">
                                                                @{{ row.unit }}
                                                            </td>
                                                            <td style="width: 180px">
                                                                <input type="number" v-model="row.balance"
                                                                       :name="'products['+index+'][balance]'"
                                                                       class="form-control input-sm" readonly required>
                                                            </td>
                                                            <td style="width: 180px">
                                                                <input type="number" v-model="row.quantity"
                                                                       :name="'products['+index+'][quantity]'"
                                                                       class="form-control input-sm"
                                                                       @change="itemtotal(row);valid(row)" required>
                                                            </td>
                                                            <td style="width: 180px">
                                                                <input type="number" v-model="row.rate"
                                                                       :name="'products['+index+'][rate]'"
                                                                       class="form-control input-sm"
                                                                       @change="itemtotal(row)" readonly required>
                                                            </td>
                                                            <td style="width: 180px">
                                                                <input type="text" class="form-control input-sm"
                                                                       v-bind:value="itemtotal(row)" readonly>
                                                            </td>
                                                            <td style="width: 10px">
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                        @click="delete_row(row)"><i
                                                                        class="fa fa-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                        <tfoot>
                                                        <tr>
                                                            <td colspan="6">

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
                                        class="fa fa-fw fa-lg fa-check-circle"></i>Update
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
                        get_item_balance_info_url: "{{ url('fetch-item-available-balance') }}",
                        get_item_info_url: "{{ url('fetch-item-info-rm-consumption') }}",
                        get_edit_data_url: "{{ url('fetch-consumption-by-id') }}",

                        get_old_items_data: "{{ url('fetch-purchase-products-info') }}",
                    },
                    action: {{$store_url}},
                    serial_no: {{$serial_no}},
                    consumption_id: '{{ $consumption->id }}',
                    date: '{{$consumption->date}}',
                    store_id: '{{$consumption->store_id}}',
                    batch_id: '{{$consumption->batch_id}}',
                    group_id: '',
                    item_id: '',
                    remark: '{{$consumption->remark}}',
                    balance: '',
                    reference_no: '{{$consumption->reference_no}}',
                    items: [],
                    selected_items: [],
                    pageLoading: false
                },
                components: {
                    vuejsDatepicker
                },
                computed: {

                    subtotal: function () {
                        return this.selected_items.reduce((total, item) => {
                            return total + (item.quantity * item.rate)
                        }, 0)
                    }
                },
                methods: {

                    fetch_product() {

                        var vm = this;

                        var slug = vm.group_id;

                        if (slug) {
                            vm.item_id = '';
                            vm.balance = '';
                            vm.items = [];
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
                    fetch_item_balance() {

                        var vm = this;

                        var store_id = vm.store_id;
                        var item_id = vm.item_id;
                        if (!vm.store_id) {
                            toastr.error('Please Select Store', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        }
                        if (item_id) {
                            vm.pageLoading = true;
                            axios.get(this.config.get_item_balance_info_url + '/' + item_id + '/' + store_id).then(function (response) {

                                vm.balance = response.data.balance;
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
                        var slug = vm.item_id;

                        if (!vm.store_id) {
                            toastr.error('Please Select Store', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        }
                        if (!slug) {
                            toastr.error('Please Select Item', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        }

                        var exists = vm.selected_items.some(function (field) {
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
                                    let data = response.data;
                                    console.log(data);
                                    vm.selected_items.push({
                                        id: data.item.id,
                                        group: data.item.parent.name,
                                        name: data.item.name,
                                        unit: data.item.unit.name,
                                        balance: data.balance,
                                        rate: data.average_rate,
                                        quantity: '',
                                    });
                                    vm.balance = '';
                                    vm.item_id = '';
                                    vm.group_id = '';
                                    vm.items = [];
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

                        var slug = vm.serial_no;

                        if (slug) {
                            vm.pageLoading = true;
                            axios.get(this.config.get_edit_data_url + '/' + slug).then(function (response) {

                                // vm.selected_items = response.data.products;
                                vm.date = '2024-04-23';
                                vm.store_id = response.data.store_id;
                                vm.batch_id = response.data.batch_id;
                                vm.reference_no = response.data.reference_no;
                                vm.remark = response.data.remark;
                                vm.selected_items = response.data.items;
                                vm.pageLoading = false;
                            }).catch(function (error) {
                                vm.pageLoading = false;
                                toastr.error('Something went to wrong', {
                                    closeButton: true,
                                    progressBar: true,
                                });

                                return false;

                            });
                        }

                    },
                    load_old() {
                        var vm = this;
                        var slug = vm.consumption_id;
                        //  alert(slug);
                        axios.get(this.config.get_edit_data_url + '/' + slug).then(function (response) {
                            var item = response.data.items;
                            for (key in item) {
                                vm.selected_items.push(item[key]);
                            }
                        })
                    },
                    delete_row: function (row) {
                        this.selected_items.splice(this.selected_items.indexOf(row), 1);
                    },
                    itemtotal: function (index) {
                        return parseFloat(index.quantity * index.rate).toFixed(2);


                        //   alert(quantity);
                        //  var total= row.quantity);
                        //  row.itemtotal=total;
                    },
                    valid: function (index) {
                        //console.log(index.stock_quantity);
                        if (index.quantity > index.balance) {
                            //console.log('1st');
                            index.quantity = index.balance;
                        }
                        if (index.quantity <= 0) {
                            //console.log('3');
                            index.quantity = '';
                        }
                    },

                },
                beforeMount() {
                    this.load_old();
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
