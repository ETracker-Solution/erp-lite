@extends('layouts.app')

@section('title', 'FG Requisition Delivery Entry')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'FG Requisition Delivery' => route('fg-requisition-deliveries.index'),
            'Create' => '',
        ];
        $prefillRequisitionId = request('requisition_id') ? (string) request('requisition_id') : '';
    @endphp
    <x-breadcrumb title="FG Requisition Delivery" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <div class="col-lg-10 offset-lg-1">
                    <form action="{{ route('fg-requisition-deliveries.store') }}" method="POST"
                          class="prevent-enter-submit">
                        @csrf
                        <input type="hidden" name="submission_token"
                               value="{{ session()->get('submission_token') ?? Str::random(40) }}">

                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title mb-0">New FG Requisition Delivery</h3>
                                <div class="card-tools">
                                    <a href="{{ route('fg-requisition-deliveries.index') }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fa fa-list"></i> List
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    Deliver approved FG requisition from factory to outlet. FGRD No generates on save.
                                </p>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="requisition_id">Requisition (FGR) <span class="text-danger">*</span></label>
                                            <select name="requisition_id" id="requisition_id" class="form-control bSelect"
                                                    v-model="requisition_id" required @change="onRequisitionChange($event)">
                                                <option value="">Select requisition</option>
                                                @foreach($requisitions as $row)
                                                    <option value="{{ $row->id }}"
                                                        data-from-store-id="{{ $row->from_store_id }}"
                                                        data-to-store-id="{{ $row->to_store_id }}"
                                                        {{ $prefillRequisitionId === (string) $row->id ? 'selected' : '' }}>
                                                        {{ $row->uid ?: ('#'.$row->id) }}
                                                        @if($row->date) — {{ $row->date }}@endif
                                                    </option>
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
                                            <label for="delivery_status">Delivery Status <span class="text-danger">*</span></label>
                                            <select name="delivery_status" id="delivery_status" class="form-control"
                                                    v-model="delivery_status" required>
                                                <option value="full">Full Delivery</option>
                                                <option value="partial">Partial</option>
                                                <option value="close">Partial & Close</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="from_store_id">From Store (Factory) <span class="text-danger">*</span></label>
                                            <select name="from_store_id" id="from_store_id" class="form-control bSelect"
                                                    v-model="from_store_id" required @change="reloadItems">
                                                <option value="">Select store</option>
                                                @foreach($from_stores as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="to_store_id">To Store (Outlet) <span class="text-danger">*</span></label>
                                            <select name="to_store_id" id="to_store_id" class="form-control bSelect"
                                                    v-model="to_store_id" required>
                                                <option value="">Select store</option>
                                                @foreach($to_stores as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="reference_no">Reference No</label>
                                            <input type="text" class="form-control" id="reference_no"
                                                   name="reference_no" v-model="reference_no" placeholder="Optional">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="remark">Remark</label>
                                            <input type="text" class="form-control" id="remark" name="remark"
                                                   v-model="remark" placeholder="Optional note">
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-1 mb-3">

                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0 font-weight-bold">Delivery Items</h6>
                                    <span class="text-muted small" v-if="pageLoading">Loading…</span>
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
                                            <th class="text-right" style="width:12%">Req Qty</th>
                                            <th class="text-right" style="width:14%">Deliver Qty</th>
                                            <th style="width:5%"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="!pageLoading && items.length === 0">
                                            <td colspan="8" class="text-center text-muted py-4">
                                                Select an approved requisition to load lines.
                                            </td>
                                        </tr>
                                        <tr v-for="(row, index) in items" :key="row.coi_id">
                                            <td>@{{ index + 1 }}</td>
                                            <td>@{{ row.group }}</td>
                                            <td>
                                                <input type="hidden" :name="'products['+index+'][coi_id]'"
                                                       :value="row.coi_id">
                                                <input type="hidden" :name="'products['+index+'][rate]'"
                                                       :value="row.fg_average_rate">
                                                @{{ row.name }}
                                            </td>
                                            <td>@{{ row.unit }}</td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm text-right"
                                                       v-model="row.balance_quantity"
                                                       :name="'products['+index+'][balance_quantity]'"
                                                       readonly required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm text-right"
                                                       v-model="row.requisition_quantity"
                                                       :name="'products['+index+'][requisition_quantity]'"
                                                       readonly required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0"
                                                       class="form-control form-control-sm text-right"
                                                       v-model="row.quantity"
                                                       :name="'products['+index+'][quantity]'"
                                                       @change="valid(row)" required>
                                            </td>
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
                                            <th colspan="6" class="text-right">Total Deliver Qty</th>
                                            <th class="text-right">
                                                @{{ Number(total_quantity).toFixed(2) }}
                                                <input type="hidden" name="total_quantity" :value="total_quantity">
                                                <input type="hidden" name="total_item" :value="items.length">
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
                        get_old_items_data: "{{ url('fetch-requisition-by-id') }}",
                    },
                    requisition_id: @json($prefillRequisitionId),
                    date: "{{ date('Y-m-d') }}",
                    reference_no: '',
                    remark: '',
                    delivery_status: 'full',
                    from_store_id: '',
                    to_store_id: '',
                    items: [],
                    pageLoading: false,
                },
                computed: {
                    total_quantity: function () {
                        return this.items.reduce(function (total, item) {
                            return total + (parseFloat(item.quantity) || 0);
                        }, 0);
                    },
                },
                mounted: function () {
                    if (this.requisition_id) {
                        this.$nextTick(function () {
                            this.onRequisitionChange();
                            $('.bSelect').selectpicker('refresh');
                        }.bind(this));
                    }
                },
                methods: {
                    delete_row: function (row) {
                        this.items.splice(this.items.indexOf(row), 1);
                    },
                    onRequisitionChange: function (event) {
                        var vm = this;
                        var selectedId = event && event.target ? event.target.value : vm.requisition_id;
                        vm.requisition_id = selectedId;
                        vm.pageLoading = true;

                        if (!selectedId) {
                            vm.items = [];
                            vm.pageLoading = false;
                            return;
                        }

                        var select = document.getElementById('requisition_id');
                        var option = select && select.options ? select.options[select.selectedIndex] : null;
                        var factoryStoreId = option ? option.getAttribute('data-to-store-id') : '';
                        var outletStoreId = option ? option.getAttribute('data-from-store-id') : '';

                        // The option already contains the requisition stores, so load items once.
                        if (factoryStoreId) {
                            vm.from_store_id = String(factoryStoreId);
                            vm.to_store_id = String(outletStoreId || '');
                            vm.$nextTick(function () {
                                $('.bSelect').selectpicker('refresh');
                                vm.reloadItems();
                            });
                            return;
                        }

                        axios.get(this.config.get_old_items_data + '/' + selectedId)
                            .then(function (response) {
                                // Delivery: Factory (requisition.to) -> Outlet (requisition.from)
                                vm.from_store_id = String(response.data.to_store_id || '');
                                vm.to_store_id = String(response.data.from_store_id || '');
                                vm.date = response.data.date || vm.date;
                                vm.reference_no = response.data.reference_no || '';
                                vm.remark = response.data.remark || '';
                                vm.$nextTick(function () {
                                    $('.bSelect').selectpicker('refresh');
                                    vm.reloadItems();
                                });
                            })
                            .catch(function () {
                                vm.pageLoading = false;
                                toastr.error('Failed to load requisition', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                            });
                    },
                    reloadItems: function () {
                        var vm = this;
                        if (!vm.requisition_id) {
                            vm.pageLoading = false;
                            return;
                        }
                        vm.pageLoading = true;
                        var url = this.config.get_old_items_data + '/' + vm.requisition_id;
                        if (vm.from_store_id) {
                            url += '/' + vm.from_store_id;
                        }
                        axios.get(url)
                            .then(function (response) {
                                vm.date = response.data.date || vm.date;
                                vm.reference_no = response.data.reference_no || '';
                                vm.remark = response.data.remark || '';
                                vm.items = [];
                                var item = response.data.items || [];
                                for (var key in item) {
                                    if (Object.prototype.hasOwnProperty.call(item, key)) {
                                        vm.items.push(item[key]);
                                    }
                                }
                                vm.pageLoading = false;
                            })
                            .catch(function () {
                                vm.pageLoading = false;
                                toastr.error('Failed to load items', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                            });
                    },
                    valid: function (row) {
                        var qty = parseFloat(row.quantity) || 0;
                        var balance = parseFloat(row.balance_quantity) || 0;
                        var reqQty = parseFloat(row.requisition_quantity) || 0;
                        if (qty > balance) {
                            row.quantity = balance;
                            qty = balance;
                        }
                        if (qty > reqQty) {
                            row.quantity = reqQty;
                            qty = reqQty;
                        }
                        if (qty <= 0) {
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
