@extends('layouts.app')

@section('title', 'FG Delivery Receive Entry')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'FG Delivery Receive' => route('fg-delivery-receives.index'),
            'Create' => '',
        ];
        $prefillDeliveryId = request('requisition_delivery_id') ? (string) request('requisition_delivery_id') : '';
    @endphp
    <x-breadcrumb title="FG Delivery Receive" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <div class="col-lg-10 offset-lg-1">
                    <form action="{{ route('fg-delivery-receives.store') }}" method="POST" class="prevent-enter-submit">
                        @csrf
                        <input type="hidden" name="submission_token"
                               value="{{ session()->get('submission_token') ?? Str::random(40) }}">

                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title mb-0">New FG Delivery Receive</h3>
                                <div class="card-tools">
                                    <a href="{{ route('fg-delivery-receives.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list"></i> List
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    Receive a completed FG requisition delivery into the outlet store.
                                </p>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="requisition_delivery_id">Delivery (FGRD) <span class="text-danger">*</span></label>
                                            <select name="requisition_delivery_id" id="requisition_delivery_id"
                                                    class="form-control bSelect"
                                                    v-model="requisition_delivery_id" required @change="onDeliveryChange">
                                                <option value="">Select delivery</option>
                                                @foreach($requisition_deliveries as $row)
                                                    <option value="{{ $row->id }}"
                                                        {{ $prefillDeliveryId === (string) $row->id ? 'selected' : '' }}>
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
                                            <label for="reference_no">Reference No</label>
                                            <input type="text" class="form-control" id="reference_no"
                                                   name="reference_no" v-model="reference_no" placeholder="Optional">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="from_store_id">From Store (Factory) <span class="text-danger">*</span></label>
                                            <input type="hidden" name="from_store_id" :value="from_store_id">
                                            <select id="from_store_id" class="form-control bSelect"
                                                    v-model="from_store_id" disabled>
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
                                            <input type="hidden" name="to_store_id" :value="to_store_id">
                                            <select id="to_store_id" class="form-control bSelect"
                                                    v-model="to_store_id" disabled>
                                                <option value="">Select store</option>
                                                @foreach($to_stores as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
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
                                    <h6 class="mb-0 font-weight-bold">Receive Items</h6>
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
                                            <th class="text-right" style="width:14%">Deliver Qty</th>
                                            <th class="text-right" style="width:14%">Receive Qty</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="!pageLoading && items.length === 0">
                                            <td colspan="6" class="text-center text-muted py-4">
                                                Select a completed delivery to load lines.
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
                                                       v-model="row.delivery_quantity"
                                                       :name="'products['+index+'][delivery_quantity]'"
                                                       readonly required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0"
                                                       class="form-control form-control-sm text-right"
                                                       v-model="row.quantity"
                                                       :name="'products['+index+'][quantity]'"
                                                       @change="valid(row)" required>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot v-if="items.length > 0">
                                        <tr class="bg-light">
                                            <th colspan="5" class="text-right">Total Receive Qty</th>
                                            <th class="text-right">
                                                @{{ Number(total_quantity).toFixed(2) }}
                                                <input type="hidden" name="total_quantity" :value="total_quantity">
                                                <input type="hidden" name="total_item" :value="items.length">
                                            </th>
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
                        get_old_items_data: "{{ url('fetch-requisition-delivery-by-id') }}",
                    },
                    requisition_delivery_id: @json($prefillDeliveryId),
                    date: "{{ date('Y-m-d') }}",
                    reference_no: '',
                    remark: '',
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
                    if (this.requisition_delivery_id) {
                        this.$nextTick(function () {
                            this.onDeliveryChange();
                            $('.bSelect').selectpicker('refresh');
                        }.bind(this));
                    }
                },
                methods: {
                    onDeliveryChange: function () {
                        var vm = this;
                        if (!vm.requisition_delivery_id) {
                            vm.items = [];
                            return;
                        }
                        vm.pageLoading = true;
                        axios.get(this.config.get_old_items_data + '/' + vm.requisition_delivery_id)
                            .then(function (response) {
                                vm.items = [];
                                var item = response.data.items || [];
                                for (var key in item) {
                                    if (Object.prototype.hasOwnProperty.call(item, key)) {
                                        vm.items.push(item[key]);
                                    }
                                }
                                vm.from_store_id = String(response.data.from_store_id || '');
                                vm.to_store_id = String(response.data.to_store_id || '');
                                vm.date = response.data.date || vm.date;
                                vm.reference_no = response.data.reference_no || '';
                                vm.remark = response.data.remark || '';
                                vm.pageLoading = false;
                                vm.$nextTick(function () {
                                    $('.bSelect').selectpicker('refresh');
                                });
                            })
                            .catch(function () {
                                vm.pageLoading = false;
                                toastr.error('Failed to load delivery', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                            });
                    },
                    valid: function (row) {
                        var qty = parseFloat(row.quantity) || 0;
                        var delivered = parseFloat(row.delivery_quantity) || 0;
                        if (qty > delivered) {
                            row.quantity = delivered;
                            qty = delivered;
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
