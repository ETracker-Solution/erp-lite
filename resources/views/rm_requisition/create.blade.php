@extends('layouts.app')
@section('title')
    RM Requisition
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'RM Requisition Entry'=>''
        ]
    @endphp
    <x-breadcrumb title='RM Requisition Entry' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                  <span v-if="pageLoading" class="pageLoader">
                            <img src="{{ asset('loading.gif') }}" alt="loading">
                        </span>
                <div class="col-lg-12 col-md-12">
                    <form action="{{ route('rm-requisitions.store') }}" method="POST" class="">
                        @csrf
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">RM Requisition Entry</h3>
                                <div class="card-tools">
                                    <a href="{{route('rm-requisitions.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list" aria-hidden="true"></i> &nbsp;See List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="card-box">
                                    <div id="">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="requisition_no">RMR No</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control input-sm"
                                                               name="serial_no"
                                                               id="serial_no" v-model="serial_no">
                                                        {{-- <span class="input-group-append">
                                                                    <button type="button" class="btn btn-info btn-flat">Search</button>
                                                        </span> --}}
                                                    </div>
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
                                                    <label for="from_store_id"> from Store</label>
                                                    <select name="from_store_id" id="from_store_id"
                                                            class="form-control bSelect"
                                                            v-model="from_store_id" required @change="getUUID">
                                                        <option value="">Select One</option>
                                                        @foreach($from_stores as $row)
                                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="to_store_id">To Store</label>
                                                    <select name="to_store_id" id="to_store_id"
                                                            class="form-control bSelect"
                                                            v-model="to_store_id" required>
                                                        <option value="">Select One</option>
                                                        @foreach($to_stores as $row)
                                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="reference_no">Reference No</label>
                                                    <input type="text" class="form-control input-sm"
                                                           placeholder="Enter Reference No"
                                                           value="{{old('reference_no')}}" name="reference_no">
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
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
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">RM Requisition Line Item</h3>
                                <div class="card-tools">
                                    <a href="{{route('rm-requisitions.index')}}">

                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="card-box">
                                    <div id="">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label for="group_id" class="control-label">Group</label>
                                                    <select class="form-control bSelect" name="group_id"
                                                            v-model="group_id" @change="fetch_items">
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
                                                    <select name="item_id" id="item_id" class="form-control bSelect"
                                                            v-model="item_id">
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
                                                        <thead class="bg-secondary">
                                                        <tr>
                                                            <th style="width: 5%">#</th>
                                                            <th style="width: 20%">Group</th>
                                                            <th style="width:35%">Item</th>
                                                            <th style="width: 6%">Unit</th>
                                                            <th style="width: 8%;vertical-align: middle">Quantity</th>
                                                            <th style="width: 6%"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(row, index) in selected_items">
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
                                                                @{{ row.uom }}
                                                            </td>
                                                            <td style="vertical-align: middle" class="text-right">
                                                                <input type="number" v-model="row.quantity"
                                                                       :name="'products['+index+'][quantity]'"
                                                                       class="form-control input-sm"
                                                                       @change="valid(row)" required>
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
                                                            <td colspan="6" style="background-color: #DDDCDC">

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" class="text-right">
                                                                Total Quantity
                                                            </td>
                                                            <td class="text-right">
                                                                @{{total_quantity}}
                                                                <input type="hidden" :name="'total_quantity'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="total_quantity" readonly>
                                                                <input type="hidden" :name="'total_item'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="selected_items.length" readonly>
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
                            </div>
                            <div class="card-footer">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right"
                                     v-if="selected_items.length > 0">
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
                        get_item_info_url: "{{ url('fetch-item-by-id-for-rm-requisition') }}",
                    },
                    date: new Date(),
                    serial_no: "{{$serial_no ?? 'Please Select Store First'}}",
                    customer_id: '',
                    from_store_id: '',
                    to_store_id: '',
                    group_id: '',
                    item_id: '',
                    products: [],
                    selected_items: [],
                    pageLoading: false,

                },
                components: {
                    vuejsDatepicker
                },
                computed: {

                    total_quantity: function () {
                        return this.selected_items.reduce((total, item) => {
                            return total + parseFloat(item.quantity ? item.quantity : 0)
                        }, 0)
                    },

                },
                methods: {

                    fetch_items() {

                        let vm = this;

                        let slug = vm.group_id;
                        //    alert(slug);
                        if (slug) {
                            axios.get(this.config.get_items_info_by_group_id_url + '/' + slug).then(function (response) {
                                vm.products = [];
                                vm.item_id = '';
                                console.log('products.....empty');
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

                        let vm = this;
                        if (!vm.group_id) {
                            toastr.error('Please Select Group', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        } else {
                            let item_id = vm.item_id;
                            let exists = vm.selected_items.some(function (field) {
                                return field.id == item_id
                            });

                            if (exists) {
                                toastr.info('Item Already Selected', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                            } else {
                                if (item_id) {
                                    axios.get(this.config.get_item_info_url + '/' + item_id).then(function (response) {

                                        let product_details = response.data;
                                        vm.selected_items.push({
                                            coi_id: product_details.coi_id,
                                            group: product_details.group,
                                            name: product_details.name,
                                            uom: product_details.unit.name,
                                            balance_qty: product_details.balance_qty,
                                            price: product_details.price,
                                            quantity: '',

                                        });

                                        // vm.item_id = '';
                                        // vm.group_id = '';

                                    }).catch(function (error) {

                                        toastr.error('Something went to wrong', {
                                            closeButton: true,
                                            progressBar: true,
                                        });

                                        return false;

                                    });
                                } else {

                                    vm.pageLoading = true;
                                    axios.get(this.config.get_items_info_by_group_id_url + '/' + vm.group_id).then(function (response) {
                                        vm.selected_items = [];
                                        vm.item_id = '';
                                        let items = response.data.products;
                                        for (let key in items) {
                                            vm.selected_items.push(items[key]);
                                        }
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
                        }
                    },

                    delete_row: function (row) {
                        this.selected_items.splice(this.selected_items.indexOf(row), 1);
                    },
                    valid: function (index) {

                        console.log(index.quantity);
                        if (index.quantity <= 0) {
                            //console.log('3');
                            index.quantity = '';
                        }
                    },
                    getUUID() {
                        const vm = this
                        if (!vm.from_store_id) {
                            vm.serial_no = 'Please Select Store First'
                            toastr.error('Please Select valid Store', {
                                closeButton: true,
                                progressBar: true,
                            });
                        }
                        axios.get('/get-uuid/' + vm.from_store_id, {
                            params: {
                                model: "requisition",
                                column: 'uid',
                                is_factory: true
                            }
                        }).then((response) => {
                            vm.serial_no = response.data
                        })
                    }
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
