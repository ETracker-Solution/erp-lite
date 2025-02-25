@extends('layouts.app')
@section('title', 'Goods Purchase')

@section('content')
    <!-- Content Header (Page header) -->
    @php
$links = [
    'Home' => route('dashboard'),
    'Purchase Entry' => ''
]
    @endphp
    <x-breadcrumb title='Purchase' :links="$links" />
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <span v-if="pageLoading" class="categoryLoader">
                    <img src="{{ asset('loading.gif') }}" alt="loading">
                </span>
                <div class="col-lg-12 col-md-12">
                    <form action="{{ route('purchases.store') }}" method="POST" class="">
                        @csrf
                        <input type="hidden" name="submission_token"
                            value="{{ session()->get('submission_token') ?? Str::random(40) }}">
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Goods Purchase Bill (GPB) Entry</h3>
                                <div class="card-tools">
                                    <a href="{{route('purchases.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-bars" aria-hidden="true"></i> &nbsp;
                                        See List
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="card-box">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="uid">Purchase No</label>
                                                <input type="text" class="form-control input-sm" name="uid" v-model="uid"
                                                    id="uid" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="date">Date</label>
                                                <vuejs-datepicker v-model="date" name="date" placeholder="Select date"
                                                    format="yyyy-MM-dd"></vuejs-datepicker>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="store_id">Store</label>
                                                <select name="store_id" id="store_id" class="form-control bSelect"
                                                    v-model="store_id" required>
                                                    <option value="">Select Store</option>
                                                    @foreach($stores as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="reference_no">Reference No</label>
                                                <input type="text" class="form-control input-sm"
                                                    value="{{old('reference_no')}}" name="reference_no">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="supplier_id">Group</label>
                                                <select name="supplier_group_id" id="supplier_group_id"
                                                    class="form-control bSelect" v-model="supplier_group_id"
                                                    @change="fetch_supplier">
                                                    <option value="">Select Group</option>
                                                    @foreach($supplier_groups as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="supplier_id">Supplier</label>
                                                <select name="supplier_id" id="supplier_id" class="form-control bSelect"
                                                    v-model="supplier_id" required>
                                                    <option value="">Select Supplier</option>
                                                    <option :value="row . id" v-for="row in suppliers" v-html="row.name">
                                                    </option>

                                                </select>
                                                </select>
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
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Goods Purchase Line Item</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="item_id">Item</label>
                                            <select name="item_id" id="item_id" class="form-control bSelect"
                                                v-model="item_id" @change="updateGroupId">
                                                <option value="">Select one</option>
                                                <option :value="row . id" v-for="row in items" v-html="row.name">
                                                </option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="margin-top: 30px;">
                                        <button type="button" class="btn btn-info btn-block" @click="data_input"
                                            :disabled="!store_id">Add
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" v-if="selected_items.length>0">

                                        <hr>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="bg-secondary">
                                                    <tr>
                                                        <th style="width: 10px">#</th>
                                                        <th style="width: 200px">Group</th>
                                                        <th>Item</th>
                                                        <th style="width: 50px">Unit</th>
                                                        <th style="width: 180px">Unit per Alt unit</th>
                                                        <th style="width: 180px">Alter Unit</th>
                                                        <th style="width: 180px">Alter Qty</th>
                                                        <th style="width: 180px">Unit qty</th>
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
                                                            <input type="hidden" :name="'products[' + index + '][coi_id]'"
                                                                class="form-control input-sm" v-bind:value="row.id">

                                                        </td>
                                                        <td>
                                                            @{{ row.uom }}
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control input-sm"
                                                                :name="'products[' + index + '][a_unit_quantity]'"
                                                                v-model="row.a_unit_quantity" @change="itemtotal(row);valid_quantity(row)">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control input-sm" :value="row . alter_unit" :name="'products[' + index + '][alter_unit]'"
                                                                v-if="row.alter_unit" readonly>
                                                            <input type="hidden" class="form-control input-sm" :value="row . alter_unit_id" :name="'products[' + index + '][alter_unit_id]'" v-if="row.alter_unit_id" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" v-model="row.quantity" :name="'products[' + index + '][quantity]'" class="form-control input-sm"
                                                                v-if="row.alter_unit"
                                                                @change="itemtotal(row);valid_quantity(row)" required>
                                                        </td>
                                                        <td>
                                                            @{{ convertedUnit(row) }}
                                                            <input type="hidden" class="form-control input-sm"
                                                                :name="'products[' + index + '][converted_unit_qty]'"
                                                                class="form-control input-sm" step="any"
                                                                v-bind:value="convertedUnit(row)" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" v-model="row.rate" :name="'products[' + index + '][rate]'" class="form-control input-sm" step="any"
                                                                @change="itemtotal(row);valid_rate(row)" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control input-sm"
                                                                v-bind:value="itemtotal(row)" readonly>
                                                            <input type="hidden" class="form-control input-sm"
                                                                :name="'products[' + index + '][value_amount]'"
                                                                class="form-control input-sm" step="any"
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
                                                        <td colspan="8" style="background-color: #DDDCDC">

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5">

                                                        </td>
                                                        <td>
                                                            Subtotal
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control input-sm" name="subtotal"
                                                                v-bind:value="subtotal" readonly>
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
                                                            <input type="text" name="vat" class="form-control input-sm"
                                                                v-model="vat">
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5">
                                                        </td>
                                                        <td>
                                                            Discount
                                                        </td>
                                                        <td>
                                                            <input type="text" name="discount" class="form-control input-sm"
                                                                v-model="discount">
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
                                                                name="net_payable" v-bind:value="net_payable" readonly>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <tfoot>
                                            </table>
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
                    },
                    date: new Date(),
                    vat: 0,
                    discount: 0,
                    quantity: 0,
                    rate: 0,
                    supplier_group_id: '',
                    supplier_id: '',
                    group_id: '',
                    item_id: '',
                    items: [],
                    suppliers: [],
                    selected_items: [],
                    alter_unit: '',
                    a_unit_quantity: 0,
                    pageLoading: false,
                    uid: "{{$uid}}",
                    store_id: '',
                    isDisabled: false
                },
                components: {
                    vuejsDatepicker
                },
                computed: {

                    subtotal: function () {
                        return this.selected_items.reduce((total, item) => {
                            let quantity = 0
                            if (item.alter_unit_id){
                                quantity = parseFloat(item.quantity);
                            }else{
                                quantity = parseFloat(item.a_unit_quantity)
                            }
                            const rate = parseFloat(item.rate);
                            return total + quantity * rate;
                        }, 0);
                    },
                    net_payable: function () {
                        const subtotal = parseFloat(this.subtotal);
                        const vat = parseFloat(this.vat);
                        const discount = parseFloat(this.discount);
                        return subtotal + vat - discount;
                    },
                },
                methods: {
                    updateGroupId() {
                        const selectedItem = this.items.find(item => item.id == this.item_id);
                        this.group_id = selectedItem ? selectedItem.parent_id : '';
                    },

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
                        vm.pageLoading = true;

                        axios.get(this.config.get_items_info_by_group_id_url, {
                            params: {
                                rootAccountType: 'RM'
                            }
                        }).then(function (response) {
                            vm.items = response.data.products;
                            vm.pageLoading = false;
                        }).catch(function (error) {
                            toastr.error('Something went wrong', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });
                    },
                    data_input() {

                        let vm = this;
                        if (!vm.group_id) {
                            toastr.error('Please Select Group', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        }
                        else {
                            vm.isDisabled = true
                            let group_id = vm.group_id;
                            let item_id = vm.item_id;

                            if (item_id) {
                                vm.pageLoading = true;

                                axios.get(vm.config.get_item_info_url + '/' + item_id, {
                                    params: { rootAccountType: 'RM' }
                                }).then(response => {
                                    let item_info = response.data;

                                    let lastItem = item_info.purchase_items.length > 0 ? item_info.purchase_items[item_info.purchase_items.length - 1] : '';
                                    console.log(lastItem);
                                    console.log(item_info.purchase_items);

                                    vm.selected_items.push({
                                        id: item_info.id,
                                        group: item_info.parent?.name || '',
                                        name: item_info.name,
                                        uom: item_info.unit?.name || '',
                                        alter_unit: item_info.alter_unit?.name || '',
                                        alter_unit_id: item_info.alter_unit?.id || '',
                                        value_amount: lastItem?.value_amount ?? 0,
                                        alt_unit_rate: lastItem?.alt_unit_rate ?? 0,
                                        a_unit_quantity: lastItem?.a_unit_quantity ?? 0,
                                        rate: item_info.alter_unit === null ? 0 : item_info.purchase_items.length > 0 ? item_info.purchase_items[item_info.purchase_items.length - 1].alt_unit_rate : 0,
                                        quantity: item_info.alter_unit === null ? 0 : (lastItem?.quantity ?? 1) / (lastItem?.a_unit_quantity ?? 1),                                    });

                                    console.log(vm.selected_items);

                                    vm.pageLoading = false;
                                    vm.isDisabled = false;

                                    toastr.success('Added New Item', {
                                        closeButton: true,
                                        progressBar: true,
                                    });

                                }).catch(error => {
                                    toastr.error('Something went wrong', {
                                        closeButton: true,
                                        progressBar: true,
                                    });

                                    vm.pageLoading = false;
                                    vm.isDisabled = false;
                                });
                            } else {
                                vm.pageLoading = true;
                                axios.get(this.config.get_items_info_by_group_id_url + '/' + vm.group_id).then(function (response) {
                                    let items = response.data.products;
                                    for (let key in items) {
                                        // let exists = vm.selected_items.some(function (field) {
                                        //     return field.coi_id == items[key].id
                                        // });
                                        // if (exists) {
                                        //     vm.pageLoading = false;
                                        //     toastr.error('Item Already Selected Fom this group', {
                                        //         closeButton: true,
                                        //         progressBar: true,
                                        //     });
                                        //     vm.isDisabled = false;
                                        //     return
                                        // }
                                        vm.selected_items.push(items[key]);
                                    }
                                    vm.isDisabled = false;
                                    vm.pageLoading = false;

                                }).catch(function (error) {

                                    toastr.error('Something went to wrong', {
                                        closeButton: true,
                                        progressBar: true,
                                    });
                                    vm.isDisabled = false;
                                    return false;

                                });
                            }
                        }

                    },
                    delete_row: function (row) {
                        this.selected_items.splice(this.selected_items.indexOf(row), 1);
                    },
                    itemtotal: function (index) {
                        let quantity = 0
                        if (index.alter_unit_id){
                            quantity = parseFloat(index.quantity);
                        }else{
                            quantity = parseFloat(index.a_unit_quantity)
                        }
                        const rate = parseFloat(index.rate);
                        return quantity * rate;
                    },
                    convertedUnit: function (index) {
                        let unitQty = parseFloat(index.quantity == null || index.quantity == 0 ? 1 : index.quantity) *
                            parseFloat(index.a_unit_quantity == null || index.a_unit_quantity == 0 ? 1 : index.a_unit_quantity);
                        if ((index.uom === 'GM' || index.uom === 'ML' || index.uom === 'gm' || index.uom === 'ml') && unitQty >= 1000) {
                            unitQty = unitQty / 1000;

                            if (index.uom.toLowerCase() === 'gm') {
                                return `${unitQty} kg`;
                            } else if (index.uom.toLowerCase() === 'ml') {
                                return `${unitQty} ltr`;
                            }
                        }

                        return `${unitQty} ${index.uom}`;
                    },
                    valid_quantity: function (index) {
                        if (index.a_unit_quantity < 0 && index.quantity <= 0) {
                            toastr.error('Quantity 0 or Negative not Allow', {
                                closeButton: true,
                                progressBar: true,
                            });
                            index.quantity = 0;
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

                updated() {
                    $('.bSelect').selectpicker('refresh');
                },
                mounted() {
                    this.fetch_product();
                }


            });

            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });
        });
    </script>
@endpush
