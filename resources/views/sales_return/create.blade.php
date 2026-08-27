@extends('layouts.app')

@section('title', 'Sales Return Entry')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Sales Return' => route('sales-returns.index'),
            'Create' => '',
        ];
    @endphp
    <x-breadcrumb title="Sales Return" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <div class="col-lg-10 offset-lg-1">
                    <form action="{{ route('sales-returns.store') }}" method="POST" class="prevent-enter-submit">
                        @csrf
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title mb-0">New Sales Return</h3>
                                <div class="card-tools">
                                    <a href="{{ route('sales-returns.index') }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list"></i> List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    Search a sale invoice to return items. Return No generates on save.
                                </p>

                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group" style="position: relative">
                                            <label>Invoice Search <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Type invoice number"
                                                   v-model="searchquery" @keyup="autoComplete" autofocus>
                                            <ul class="list-group" style="position:absolute;width:100%;z-index:2;"
                                                v-if="data_results.length">
                                                <li class="list-group-item list-group-item-action"
                                                    style="cursor:pointer"
                                                    v-for="result in data_results" :key="result.id"
                                                    @click="selectautoCompleteInvoice(result.id)">
                                                    @{{ result.invoice_number }}
                                                    <span class="text-muted small" v-if="result.date"> — @{{ result.date }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="store_id">Store <span class="text-danger">*</span></label>
                                            <select name="store_id" id="store_id" class="form-control" required>
                                                <option value="">Select store</option>
                                                @foreach($stores as $store)
                                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
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
                                </div>

                                <hr class="mt-1 mb-3">

                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead class="thead-light">
                                        <tr>
                                            <th style="width:4%">#</th>
                                            <th>Group</th>
                                            <th>Item</th>
                                            <th style="width:8%">Unit</th>
                                            <th class="text-right" style="width:10%">Rate</th>
                                            <th class="text-right" style="width:12%">Sale Qty</th>
                                            <th class="text-right" style="width:12%">Return Qty</th>
                                            <th class="text-right" style="width:12%">Value</th>
                                            <th style="width:5%"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-if="items.length === 0">
                                            <td colspan="9" class="text-center text-muted py-4">
                                                Search and select an invoice to load returnable lines.
                                            </td>
                                        </tr>
                                        <tr v-for="(row, index) in items" :key="row.coi_id">
                                            <td>@{{ index + 1 }}</td>
                                            <td>@{{ row.group }}</td>
                                            <td>
                                                <input type="hidden" :name="'products['+index+'][coi_id]'" :value="row.coi_id">
                                                <input type="hidden" :name="'products['+index+'][discount_type]'" :value="row.discount_type">
                                                <input type="hidden" :name="'products['+index+'][discount_value]'" :value="row.discount_value">
                                                <input type="hidden" :name="'products['+index+'][discount]'" :value="row.discount_amount">
                                                @{{ row.name }}
                                            </td>
                                            <td>@{{ row.unit }}</td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm text-right"
                                                       v-model="row.rate" :name="'products['+index+'][rate]'" readonly required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm text-right"
                                                       v-model="row.sale_quantity"
                                                       :name="'products['+index+'][sale_quantity]'" readonly required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0"
                                                       class="form-control form-control-sm text-right"
                                                       v-model="row.return_quantity"
                                                       :name="'products['+index+'][quantity]'"
                                                       @change="item_total(row)" required>
                                            </td>
                                            <td class="text-right">@{{ item_total(row) }}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-xs btn-danger"
                                                        @click="delete_row(row)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mt-3" v-if="items.length > 0">
                                    <div class="col-12">
                                        <table class="table table-bordered table-sm">
                                            <thead class="thead-light">
                                            <tr>
                                                <th>Product Discount</th>
                                                <th>Coupon</th>
                                                <th>Overall</th>
                                                <th>Special</th>
                                                <th>Membership</th>
                                                <th>Total Discount</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>@{{ Number(productWiseDiscount).toFixed(2) }}</td>
                                                <td>@{{ Number(selectedCouponCodeDiscountAmount).toFixed(2) }}</td>
                                                <td>@{{ Number(selectedTotalDiscountAmount).toFixed(2) }}</td>
                                                <td>@{{ Number(selectedSpecialDiscountAmount).toFixed(2) }}</td>
                                                <td>@{{ Number(selectedMembershipDiscountAmount).toFixed(2) }}</td>
                                                <td>@{{ Number(allDiscountAmount).toFixed(2) }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-12 text-center">
                                        <h4 class="mb-0">Return Amount: @{{ Number(exchange_amount).toFixed(2) }}</h4>
                                    </div>
                                </div>

                                <input type="hidden" name="sale_id" :value="sale_data.id">
                                <input type="hidden" name="subtotal" :value="subtotal">
                                <input type="hidden" name="discount" :value="allDiscountAmount">
                                <input type="hidden" name="grand_total" :value="exchange_amount">
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

@push('script')
    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            new Vue({
                el: '#vue_app',
                data: {
                    config: {
                        get_product_info_url: "{{ url('fetch-sale-info') }}",
                    },
                    searchquery: '',
                    data_results: [],
                    items: [],
                    sale_data: {},
                    date: "{{ date('Y-m-d') }}",
                },
                computed: {
                    subtotal: function () {
                        return this.items.reduce(function (total, item) {
                            return total + ((parseFloat(item.return_quantity) || 0) * (parseFloat(item.rate) || 0));
                        }, 0);
                    },
                    productWiseDiscount: function () {
                        return this.items.reduce(function (total, item) {
                            return total + (Number(item.discount_amount) || 0);
                        }, 0);
                    },
                    total_bill: function () {
                        return this.subtotal;
                    },
                    allDiscountAmount: function () {
                        if (this.items.length < 1) return 0;
                        return Number(this.selectedTotalDiscountAmount)
                            + Number(this.selectedSpecialDiscountAmount)
                            + Number(this.selectedCouponCodeDiscountAmount)
                            + Number(this.productWiseDiscount)
                            + Number(this.selectedMembershipDiscountAmount);
                    },
                    selectedTotalDiscountAmount: function () {
                        var vm = this;
                        if (vm.items.length < 1 || !vm.sale_data) return 0;
                        if (vm.sale_data.total_discount_type === 'fixed') {
                            var saleItemCount = (vm.sale_data.items || []).length || 1;
                            var perProductDiscount = Number(vm.sale_data.total_discount_amount || 0) / saleItemCount;
                            return vm.items.reduce(function (total) {
                                return total + perProductDiscount;
                            }, 0);
                        }
                        if (vm.sale_data.total_discount_type === 'percentage') {
                            var pct = Number(vm.sale_data.total_discount_value || 0);
                            return vm.items.reduce(function (total, item) {
                                return total + (((item.return_quantity * item.rate) * pct) / 100);
                            }, 0);
                        }
                        return 0;
                    },
                    selectedSpecialDiscountAmount: function () {
                        var vm = this;
                        if (vm.items.length < 1 || !vm.sale_data || !(vm.sale_data.special_discount_amount > 0)) return 0;
                        var pct = Number(vm.sale_data.special_discount_value || 0);
                        return vm.items.reduce(function (total, item) {
                            return total + (((item.return_quantity * item.rate) * pct) / 100);
                        }, 0);
                    },
                    selectedMembershipDiscountAmount: function () {
                        var vm = this;
                        if (vm.items.length < 1 || !vm.sale_data || !(vm.sale_data.membership_discount_amount > 0)) return 0;
                        var pct = Number(vm.sale_data.membership_discount_percentage || 0);
                        return vm.items.reduce(function (total, item) {
                            return total + (((item.return_quantity * item.rate) * pct) / 100);
                        }, 0);
                    },
                    selectedCouponCodeDiscountAmount: function () {
                        var vm = this;
                        if (vm.items.length < 1 || !vm.sale_data) return 0;
                        if (vm.sale_data.couponCodeDiscountType === 'fixed') {
                            var saleItemCount = (vm.sale_data.items || []).length || 1;
                            var perProductDiscount = Number(vm.sale_data.couponCodeDiscountAmount || vm.sale_data.minimumPurchaseAmount || 0) / saleItemCount;
                            return vm.items.reduce(function (total) {
                                return total + perProductDiscount;
                            }, 0);
                        }
                        if (vm.sale_data.couponCodeDiscountType === 'percentage') {
                            var pct = Number(vm.sale_data.couponCodeDiscountValue || 0);
                            return vm.items.reduce(function (total, item) {
                                return total + (((item.return_quantity * item.rate) * pct) / 100);
                            }, 0);
                        }
                        return 0;
                    },
                    exchange_amount: function () {
                        return this.total_bill > 0 ? (this.total_bill - this.allDiscountAmount) : 0;
                    },
                },
                methods: {
                    autoComplete: function () {
                        var vm = this;
                        vm.data_results = [];
                        if (vm.searchquery.length < 2) return;
                        axios.get('/vuejs/autocomplete/sales-invoice-search', {
                            params: {searchquery: vm.searchquery}
                        }).then(function (response) {
                            vm.data_results = response.data || [];
                        });
                    },
                    selectautoCompleteInvoice: function (sale_id) {
                        var vm = this;
                        if (!sale_id) return;
                        vm.items = [];
                        axios.get(this.config.get_product_info_url + '/' + sale_id)
                            .then(function (response) {
                                var item = response.data.items || [];
                                for (var key in item) {
                                    if (Object.prototype.hasOwnProperty.call(item, key)) {
                                        if (!item[key].return_quantity) {
                                            item[key].return_quantity = item[key].quantity;
                                        }
                                        vm.items.push(item[key]);
                                    }
                                }
                                vm.sale_data = response.data.sale || {};
                                vm.date = response.data.date || vm.date;
                                vm.searchquery = '';
                                vm.data_results = [];
                            })
                            .catch(function () {
                                toastr.error('Something went to wrong', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                            });
                    },
                    delete_row: function (row) {
                        this.items.splice(this.items.indexOf(row), 1);
                    },
                    item_total: function (row) {
                        var saleQty = parseFloat(row.sale_quantity) || 0;
                        var returnQty = parseFloat(row.return_quantity) || 0;
                        if (returnQty > saleQty) {
                            row.return_quantity = saleQty;
                            returnQty = saleQty;
                        }
                        if (returnQty < 0) {
                            row.return_quantity = '';
                            returnQty = 0;
                        }
                        var total_cost = returnQty * (parseFloat(row.rate) || 0);
                        if (row.discount_type == 'p') {
                            row.discount_amount = (total_cost * (parseFloat(row.discount_value) || 0)) / 100;
                        } else if (row.discount_type == 'f') {
                            row.discount_amount = parseFloat(row.discount_value) || 0;
                        } else {
                            row.discount_amount = 0;
                        }
                        return returnQty > 0 ? (total_cost - (Number(row.discount_amount) || 0)).toFixed(2) : '0.00';
                    },
                },
            });
        });
    </script>
@endpush
