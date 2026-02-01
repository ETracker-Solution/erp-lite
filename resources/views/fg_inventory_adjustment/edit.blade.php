@extends('layouts.app')
@section('title')
    Edit FG Inventory Adjustment
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'FG Inventory Adjustment list'=>''
        ]
    @endphp
    <x-breadcrumb title='FG Inventory Adjustment Edit' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <span v-if="pageLoading" class="pageLoader">
                    <img src="{{ asset('loading.gif') }}" alt="loading">
                </span>
                <div class="col-lg-12 col-md-12">
                    <form action="{{ route('fg-inventory-adjustments.update', $fGInventoryAdjustment->id) }}" method="POST" class="">
                        @csrf
                        @method('PUT')
                        <input type="hidden" value="adjusted" name="status">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">FG Inventory Adjustment(FGIA) Edit </h3>
                                <div class="card-tools">
                                    <a class="btn btn-sm btn-primary"
                                       href="{{route('fg-inventory-adjustments.index')}}">
                                        <i class="fa fa-list" aria-hidden="true"></i> &nbsp;See
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
                                                    <label for="serial_no">FGIA No</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control input-sm"
                                                               value="{{$fGInventoryAdjustment->uid}}" name="serial_no"
                                                               id="serial_no" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="store_id">Store</label>
                                                    <select name="store_id" id="store_id"
                                                            class="form-control bSelect"
                                                            v-model="store_id" required disabled>
                                                        <option value="">Select One</option>
                                                        @foreach($stores as $row)
                                                            <option
                                                                value="{{ $row->id }}">{{ $row->id }}
                                                                - {{ $row->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="store_id" v-model="store_id">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="reference_no">Reference No</label>
                                                    <input type="text" class="form-control input-sm"
                                                           value="{{$fGInventoryAdjustment->reference_no}}" name="reference_no">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="date">Date</label>
                                                    <vuejs-datepicker v-model="date" name="date"
                                                                      placeholder="Select date"
                                                                      format="yyyy-MM-dd"></vuejs-datepicker>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label for="transaction_type">Transaction Type</label>
                                                    <select name="transaction_type" id="transaction_type"
                                                            class="form-control bSelect" v-model="transaction_type"
                                                            required>
                                                        <option value="">Select one</option>
                                                        @if(auth()->user()->is_super || (auth()->user()->employee && auth()->user()->employee->user_of == 'ho'))
                                                        <option value="increase">Increase</option>
                                                        @endif
                                                        <option value="decrease">Wastage</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="remark">Remark</label>
                                                    <textarea class="form-control" name="remark" rows="1"
                                                              placeholder="Enter Remark">{{$fGInventoryAdjustment->remark}}</textarea>
                                                </div>
                                            </div>

                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="edit_remark" class="text-danger">Reason for Edit *</label>
                                                    <textarea class="form-control" name="edit_remark" rows="2"
                                                              placeholder="Why are you editing this adjustment?" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">FGIA Item Information</h3>
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
                                                        @click="data_input" :disabled="isDisabled">Add
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
                                                            <th style="width: 3%">#</th>
                                                            <th style="width: 20%">Group</th>
                                                            <th style="width: 36%">Item</th>
                                                            <th style="width: 5%">Unit</th>
                                                            <th style="width: 15%">Balance Qty</th>
                                                            <th style="width: 8%">Rate</th>
                                                            <th style="width: 15%">Quantity</th>
                                                            <th style="width: 8%;vertical-align: middle">Value</th>
                                                            <th style="width: 5%"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(row, index) in items">
                                                            <td>
                                                                @{{ ++index }}
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
                                                            <td>
                                                                @{{ row.balance_qty }}
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][balance_qty]'" step="any"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.balance_qty" readonly>
                                                            </td>
                                                            <td class="text-right">
                                                                @{{ row.price }}
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][rate]'" step="any"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.price" readonly>
                                                            </td>
                                                            <td class="text-right">
                                                                <input type="number" v-model="row.quantity"
                                                                       step="any"
                                                                       :name="'products['+index+'][quantity]'"
                                                                       class="form-control input-sm"
                                                                       @change="valid(row);item_total(row)" required>
                                                            </td>
                                                            <td class="text-right">
                                                                @{{ item_total(row) }}
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger"
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
                                                            <td colspan="5"></td>
                                                            <td class="text-right">
                                                                SubTotal
                                                            </td>
                                                            <td class="text-right">
                                                                @{{subtotal}}
                                                                <input type="hidden" class="form-control input-sm"
                                                                       name="subtotal" v-bind:value="subtotal"
                                                                       readonly>
                                                            </td>
                                                            <td class="text-right"></td>
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
                                            class="fa fa-fw fa-lg fa-check-circle"></i>Update
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
                        get_item_info_url: "{{ url('fetch-item-by-id-for-sale') }}",
                    },

                    date: "{{ $fGInventoryAdjustment->date }}",
                    transaction_type: "{{ $fGInventoryAdjustment->transaction_type }}",
                    store_id: "{{ $fGInventoryAdjustment->store_id }}",
                    category_id: '',
                    item_id: '',
                    products: [],
                    items: {!! json_encode($fGInventoryAdjustment->items->map(function($item) {
                        return [
                            'coi_id' => $item->coi_id,
                            'group' => $item->coi->parent->name ?? 'N/A',
                            'name' => $item->coi->name,
                            'unit' => $item->coi->unit->name ?? 'N/A',
                            'balance_qty' => 0, // Should probably fetch actual balance or use saved one
                            'price' => $item->rate,
                            'quantity' => $item->quantity,
                        ];
                    })) !!},
                    isDisabled: false

                },
                components: {
                    vuejsDatepicker
                },
                mounted() {
                    // Update balance_qty for initial items
                    let vm = this;
                    vm.items.forEach(function(item) {
                        axios.get(vm.config.get_item_info_url + '/' + item.coi_id, {
                            params: {
                                store_id: vm.store_id,
                                from: 'adjustment'
                            }
                        }).then(function (response) {
                            item.balance_qty = response.data.balance_qty;
                        });
                    });
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
                        if (slug) {
                            axios.get(this.config.get_items_info_by_group_id_url + '/' + slug).then(function (response) {
                                vm.products = response.data.products;
                            });
                        }
                    },
                    data_input() {
                        let vm = this;
                        if (!vm.store_id || !vm.transaction_type || !vm.item_id) {
                            toastr.error('Missing required fields');
                            return;
                        }

                        let exists = vm.items.some(function (field) {
                            return field.coi_id == vm.item_id
                        });

                        if (exists) {
                            toastr.info('Item Already Selected');
                            return;
                        }

                        vm.isDisabled = true;
                        axios.get(this.config.get_item_info_url + '/' + vm.item_id, {
                            params: {
                                store_id: vm.store_id,
                                from: 'adjustment'
                            }
                        }).then(function (response) {
                            let product_details = response.data;
                            vm.items.push({
                                coi_id: product_details.coi_id,
                                group: product_details.group,
                                name: product_details.name,
                                unit: product_details.unit,
                                balance_qty: product_details.balance_qty,
                                price: product_details.price,
                                quantity: '',
                            });
                            vm.isDisabled = false;
                        });
                    },

                    delete_row: function (row) {
                        this.items.splice(this.items.indexOf(row), 1);
                    },
                    item_total: function (index) {
                        return (index.quantity * index.price);
                    },
                    valid: function (index) {
                        let vm = this;
                        if (index.quantity <= 0) {
                            toastr.error('Quantity 0 or Negative not Allow');
                            index.quantity = '';
                        }
                        if (vm.transaction_type == 'decrease') {
                            if (index.balance_qty < index.quantity) {
                                index.quantity = index.balance_qty;
                            }
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
