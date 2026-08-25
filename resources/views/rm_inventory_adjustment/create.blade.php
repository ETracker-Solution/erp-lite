@extends('layouts.app')

@section('title', 'RM Inventory Adjustment Entry')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'RM Inventory Adjustment' => route('rm-inventory-adjustments.index'),
            'Create' => '',
        ];
    @endphp
    <x-breadcrumb title="RM Inventory Adjustment" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <div class="col-lg-10 offset-lg-1">
                    <form action="{{ route('rm-inventory-adjustments.store') }}" method="POST" class="prevent-enter-submit">
                        @csrf
                        <input type="hidden" name="submission_token"
                               value="{{ session()->get('submission_token') ?? Str::random(40) }}">

                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title mb-0">New RM Inventory Adjustment</h3>
                                <div class="card-tools">
                                    <a href="{{ route('rm-inventory-adjustments.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list"></i> List
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    Increase stock or record wastage. UID generates on save.
                                </p>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="store_id">Store <span class="text-danger">*</span></label>
                                            <select name="store_id" id="store_id" class="form-control bSelect"
                                                    v-model="store_id" required>
                                                <option value="">Select store</option>
                                                @foreach($stores as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="date">Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="date" name="date"
                                                   v-model="date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="transaction_type">
                                                Transaction Type <span class="text-danger">*</span>
                                            </label>
                                            <select name="transaction_type" id="transaction_type"
                                                    class="form-control bSelect" v-model="transaction_type" required>
                                                <option value="">Select type</option>
                                                <option value="increase">Increase</option>
                                                <option value="decrease">Wastage</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="reference_no">Reference No</label>
                                            <input type="text" class="form-control" id="reference_no"
                                                   name="reference_no" value="{{ old('reference_no') }}"
                                                   placeholder="Optional">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="remark">Remark</label>
                                            <input type="text" class="form-control" id="remark" name="remark"
                                                   value="{{ old('remark') }}" placeholder="Optional note">
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0 font-weight-bold">Items</h6>
                                </div>

                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="category_id">Group</label>
                                            <select class="form-control bSelect" name="category_id" id="category_id"
                                                    v-model="category_id" @change="fetch_item">
                                                <option value="">Select group</option>
                                                @foreach ($groups as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="item_id">Item</label>
                                            <select name="item_id" id="item_id" class="form-control bSelect"
                                                    v-model="item_id">
                                                <option value="">Select item</option>
                                                <option :value="row.id" v-for="row in products" :key="row.id"
                                                        v-html="row.name"></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-info btn-block"
                                                    @click="data_input" :disabled="isDisabled">
                                                <i class="fa fa-plus"></i> Add Item
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead class="thead-light">
                                        <tr>
                                            <th style="width:4%">#</th>
                                            <th>Group</th>
                                            <th>Item</th>
                                            <th style="width:8%">Unit</th>
                                            <th class="text-right" style="width:12%">Balance</th>
                                            <th class="text-right" style="width:10%">Rate</th>
                                            <th class="text-right" style="width:12%">Qty</th>
                                            <th class="text-right" style="width:12%">Value</th>
                                            <th style="width:5%"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="items.length === 0">
                                            <td colspan="9" class="text-center text-muted py-4">
                                                Select group + item, then Add Item.
                                            </td>
                                        </tr>
                                        <tr v-for="(row, index) in items" :key="row.coi_id">
                                            <td>@{{ index + 1 }}</td>
                                            <td>@{{ row.group }}</td>
                                            <td>
                                                <input type="hidden" :name="'products['+index+'][coi_id]'"
                                                       :value="row.coi_id">
                                                @{{ row.name }}
                                            </td>
                                            <td>@{{ row.unit }}</td>
                                            <td class="text-right">
                                                @{{ row.balance_qty }}
                                                <input type="hidden" :name="'products['+index+'][balance_qty]'"
                                                       :value="row.balance_qty">
                                            </td>
                                            <td class="text-right">
                                                @{{ Number(row.price).toFixed(2) }}
                                                <input type="hidden" :name="'products['+index+'][rate]'"
                                                       :value="row.price">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0.01"
                                                       class="form-control form-control-sm text-right"
                                                       v-model="row.quantity"
                                                       :name="'products['+index+'][quantity]'"
                                                       @change="valid(row); item_total(row)" required>
                                            </td>
                                            <td class="text-right">@{{ Number(item_total(row)).toFixed(2) }}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-xs btn-danger"
                                                        @click="delete_row(row)" title="Remove">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot v-if="items.length > 0">
                                        <tr class="bg-light">
                                            <th colspan="6" class="text-right">Subtotal</th>
                                            <th class="text-right" colspan="2">
                                                @{{ Number(subtotal).toFixed(2) }}
                                                <input type="hidden" name="subtotal" :value="subtotal">
                                            </th>
                                            <th></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="card-footer text-right" v-show="items.length > 0">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-check-circle"></i> Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('vue-js/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endpush

@push('script')
    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script src="{{ asset('vue-js/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            new Vue({
                el: '#vue_app',
                data: {
                    config: {
                        get_items_info_by_group_id_url: "{{ url('fetch-items-by-group-id') }}",
                        get_item_info_url: "{{ url('fetch-item-by-id-for-sale') }}",
                    },
                    date: "{{ date('Y-m-d') }}",
                    transaction_type: '',
                    store_id: '',
                    category_id: '',
                    item_id: '',
                    products: [],
                    items: [],
                    isDisabled: false,
                },
                computed: {
                    subtotal: function () {
                        return this.items.reduce(function (total, item) {
                            return total + ((parseFloat(item.quantity) || 0) * (parseFloat(item.price) || 0));
                        }, 0);
                    },
                },
                methods: {
                    fetch_item: function () {
                        var vm = this;
                        vm.item_id = '';
                        vm.products = [];
                        if (!vm.category_id) {
                            return;
                        }
                        axios.get(this.config.get_items_info_by_group_id_url + '/' + vm.category_id)
                            .then(function (response) {
                                vm.products = response.data.products || [];
                            })
                            .catch(function () {
                                toastr.error('Something went to wrong', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                            });
                    },
                    data_input: function () {
                        var vm = this;
                        if (!vm.store_id) {
                            toastr.error('Enter Store', {closeButton: true, progressBar: true});
                            return;
                        }
                        if (!vm.transaction_type) {
                            toastr.error('Select transaction type', {closeButton: true, progressBar: true});
                            return;
                        }
                        if (!vm.item_id) {
                            toastr.error('Enter product', {closeButton: true, progressBar: true});
                            return;
                        }

                        var exists = vm.items.some(function (field) {
                            return field.coi_id == vm.item_id;
                        });
                        if (exists) {
                            toastr.info('Item Already Selected', {closeButton: true, progressBar: true});
                            return;
                        }

                        vm.isDisabled = true;
                        axios.get(this.config.get_item_info_url + '/' + vm.item_id, {
                            params: {store_id: this.store_id}
                        }).then(function (response) {
                            var product_details = response.data;
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
                            vm.isDisabled = false;
                        }).catch(function () {
                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });
                            vm.isDisabled = false;
                        });
                    },
                    delete_row: function (row) {
                        this.items.splice(this.items.indexOf(row), 1);
                    },
                    item_total: function (row) {
                        return (parseFloat(row.quantity) || 0) * (parseFloat(row.price) || 0);
                    },
                    valid: function (row) {
                        var vm = this;
                        if ((parseFloat(row.quantity) || 0) <= 0) {
                            toastr.error('Quantity 0 or Negative not Allow', {
                                closeButton: true,
                                progressBar: true,
                            });
                            row.quantity = '';
                            return;
                        }
                        if (vm.transaction_type === 'decrease' && row.balance_qty < row.quantity) {
                            row.quantity = row.balance_qty;
                        }
                    },
                },
                updated: function () {
                    $('.bSelect').selectpicker('refresh');
                },
            });

            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });
        });
    </script>
@endpush
