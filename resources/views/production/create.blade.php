@extends('layouts.app')

@section('title', 'FG Production Entry')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'FG Production' => route('productions.index'),
            'Create' => '',
        ];
    @endphp
    <x-breadcrumb title="FG Production" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <div class="col-lg-10 offset-lg-1">
                    <form action="{{ route('productions.store') }}" method="POST" class="prevent-enter-submit">
                        @csrf

                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title mb-0">New FG Production</h3>
                                <div class="card-tools">
                                    <a href="{{ route('productions.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list"></i> List
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    Record finished goods against a production batch. FGP No generates on save.
                                </p>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="batch_id">Batch <span class="text-danger">*</span></label>
                                            <select name="batch_id" id="batch_id" class="form-control bSelect"
                                                    v-model="batch_id" required>
                                                <option value="">Select batch</option>
                                                @foreach($batches as $row)
                                                    <option value="{{ $row->id }}">{{ $row->batch_no }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="factory_id">Production Unit <span class="text-danger">*</span></label>
                                            <select name="factory_id" id="factory_id" class="form-control bSelect"
                                                    v-model="factory_id" required>
                                                <option value="">Select factory</option>
                                                @foreach($factories as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="store_id">FG Store <span class="text-danger">*</span></label>
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
                                            <label for="reference_no">Reference No</label>
                                            <input type="text" class="form-control" id="reference_no"
                                                   name="reference_no" v-model="reference_no" placeholder="Optional">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="remark">Remark</label>
                                            <input type="text" class="form-control" id="remark" name="remark"
                                                   v-model="remark" placeholder="Optional note">
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
                                            <label for="group_id">Group</label>
                                            <select class="form-control bSelect" name="group_id" id="group_id"
                                                    v-model="group_id" @change="fetch_items">
                                                <option value="">Select group</option>
                                                @foreach ($groups as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="item_id">Item</label>
                                            <select name="item_id" id="item_id" class="form-control bSelect"
                                                    v-model="item_id">
                                                <option value="">Select item (or blank = all in group)</option>
                                                <option :value="row.id" v-for="row in items" :key="row.id"
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
                                            <th class="text-right" style="width:14%">Qty</th>
                                            <th class="text-right" style="width:14%">Rate</th>
                                            <th class="text-right" style="width:14%">Value</th>
                                            <th style="width:5%"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="selected_items.length === 0">
                                            <td colspan="8" class="text-center text-muted py-4">
                                                Select group + item, then Add Item.
                                            </td>
                                        </tr>
                                        <tr v-for="(row, index) in selected_items" :key="row.id">
                                            <td>@{{ index + 1 }}</td>
                                            <td>@{{ row.group }}</td>
                                            <td>
                                                @{{ row.name }}
                                                <input type="hidden" :name="'products['+index+'][coi_id]'"
                                                       :value="row.id">
                                            </td>
                                            <td>@{{ row.uom }}</td>
                                            <td>
                                                <input type="number" step="0.01" min="0.01"
                                                       class="form-control form-control-sm text-right"
                                                       v-model="row.quantity"
                                                       :name="'products['+index+'][quantity]'"
                                                       @change="itemtotal(row); valid_quantity(row)" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01"
                                                       class="form-control form-control-sm text-right"
                                                       v-model="row.rate"
                                                       :name="'products['+index+'][rate]'" readonly required>
                                            </td>
                                            <td class="text-right">@{{ itemtotal(row) }}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-xs btn-danger"
                                                        @click="delete_row(row)" title="Remove">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot v-if="selected_items.length > 0">
                                        <tr class="bg-light">
                                            <th colspan="4" class="text-right">Total</th>
                                            <th class="text-right">
                                                @{{ Number(total_quantity).toFixed(2) }}
                                                <input type="hidden" name="total_quantity" :value="total_quantity">
                                            </th>
                                            <th></th>
                                            <th class="text-right">
                                                @{{ Number(subtotal).toFixed(2) }}
                                                <input type="hidden" name="subtotal" :value="subtotal">
                                            </th>
                                            <th></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="card-footer text-right" v-show="selected_items.length > 0">
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
                        get_item_info_url: "{{ url('fetch-item-info') }}",
                    },
                    date: "{{ date('Y-m-d') }}",
                    factory_id: '',
                    store_id: '',
                    batch_id: '',
                    group_id: '',
                    item_id: '',
                    remark: '',
                    reference_no: '',
                    items: [],
                    selected_items: [],
                    isDisabled: false,
                },
                computed: {
                    total_quantity: function () {
                        return this.selected_items.reduce(function (total, item) {
                            return total + (parseFloat(item.quantity) || 0);
                        }, 0);
                    },
                    subtotal: function () {
                        return this.selected_items.reduce(function (total, item) {
                            return total + ((parseFloat(item.quantity) || 0) * (parseFloat(item.rate) || 0));
                        }, 0);
                    },
                },
                methods: {
                    fetch_items: function () {
                        var vm = this;
                        vm.item_id = '';
                        vm.items = [];
                        if (!vm.group_id) {
                            return;
                        }
                        axios.get(this.config.get_items_info_by_group_id_url + '/' + vm.group_id)
                            .then(function (response) {
                                vm.items = response.data.products || [];
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
                        if (!vm.batch_id) {
                            toastr.error('Please Select Batch', {closeButton: true, progressBar: true});
                            return;
                        }
                        if (!vm.factory_id) {
                            toastr.error('Please Select Production Unit', {closeButton: true, progressBar: true});
                            return;
                        }
                        if (!vm.store_id) {
                            toastr.error('Please Select Store', {closeButton: true, progressBar: true});
                            return;
                        }
                        if (!vm.group_id) {
                            toastr.error('Please Select Group', {closeButton: true, progressBar: true});
                            return;
                        }

                        vm.isDisabled = true;
                        var item_id = vm.item_id;

                        if (item_id) {
                            var exists = vm.selected_items.some(function (field) {
                                return field.id == item_id;
                            });
                            if (exists) {
                                toastr.info('Item Already Selected', {closeButton: true, progressBar: true});
                                vm.isDisabled = false;
                                return;
                            }

                            axios.get(this.config.get_item_info_url + '/' + item_id)
                                .then(function (response) {
                                    var item_info = response.data;
                                    vm.selected_items.push({
                                        id: item_info.id,
                                        group: item_info.parent ? item_info.parent.name : '',
                                        name: item_info.name,
                                        uom: item_info.unit ? item_info.unit.name : '',
                                        rate: item_info.price,
                                        quantity: item_info.quantity || '',
                                    });
                                    vm.item_id = '';
                                    vm.isDisabled = false;
                                })
                                .catch(function () {
                                    toastr.error('Something went to wrong', {
                                        closeButton: true,
                                        progressBar: true,
                                    });
                                    vm.isDisabled = false;
                                });
                            return;
                        }

                        axios.get(this.config.get_items_info_by_group_id_url + '/' + vm.group_id)
                            .then(function (response) {
                                var items = response.data.products || [];
                                for (var key in items) {
                                    if (!Object.prototype.hasOwnProperty.call(items, key)) {
                                        continue;
                                    }
                                    var product = items[key];
                                    var already = vm.selected_items.some(function (field) {
                                        return field.id == product.id;
                                    });
                                    if (!already) {
                                        vm.selected_items.push(product);
                                    }
                                }
                                vm.isDisabled = false;
                            })
                            .catch(function () {
                                toastr.error('Something went to wrong', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                vm.isDisabled = false;
                            });
                    },
                    delete_row: function (row) {
                        this.selected_items.splice(this.selected_items.indexOf(row), 1);
                    },
                    itemtotal: function (row) {
                        return ((parseFloat(row.quantity) || 0) * (parseFloat(row.rate) || 0)).toFixed(2);
                    },
                    valid_quantity: function (row) {
                        var qty = parseFloat(row.quantity) || 0;
                        if (qty <= 0) {
                            toastr.error('Quantity 0 or Negative not Allow', {
                                closeButton: true,
                                progressBar: true,
                            });
                            row.quantity = '';
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
