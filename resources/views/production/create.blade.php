@extends('layouts.app')
@section('title', 'FG Production')
@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'FG Production' => route('productions.index'),
            'FG Production Entry' => '',
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
                        <input type="hidden" name="submission_token"
                               value="{{ session()->get('submission_token') ?? Str::random(40) }}">
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">FG Production (FGP) Entry</h3>
                                <div class="card-tools">
                                    <a href="{{route('productions.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-bars"
                                           aria-hidden="true"></i> &nbsp;
                                        See List
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
                                                                       id="serial_no" v-model="serial_no">
                                                                <span class="input-group-append">
                                                                        {{-- <button type="button" class="btn btn-info btn-flat"
                                                                                @click="data_edit">Search</button> --}}
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
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="requisition_id">Requisitions</label>
                                                            <select name="requisition_id[]" id="requisition_id"
                                                                    @change="fetchRequisitionItems"
                                                                    class="form-control bSelect"
                                                                    v-model="requisition_id" multiple>
                                                                <option value="">Select Requisitions</option>
                                                                @foreach($requisitions as $row)
                                                                    <option
                                                                        value="{{ $row->id }}">{{ $row->uid }}</option>
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
                                                            <label for="factory_id">Production Unit</label>
                                                            <select name="factory_id" id="factory_id"
                                                                    class="form-control bSelect"
                                                                    v-model="factory_id" required>
                                                                <option value="">Select One</option>
                                                                @foreach($factories as $row)
                                                                    <option
                                                                        value="{{ $row->id }}">{{ $row->name }}</option>
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
                                                                <option value="">Select One</option>
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
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="rm_store_id">RM Store</label>
                                                            <select id="rm_store_id"
                                                                    class="form-control bSelect"
                                                                    v-model="rm_store_id" required>
                                                                <option value="">Select One</option>
                                                                @foreach($rm_stores as $row)
                                                                    <option
                                                                        value="{{ $row->id }}">{{ $row->id }}
                                                                        -{{ $row->name }}</option>
                                                                @endforeach
                                                            </select>
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
                                                            @change="fetch_items">
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

                                                        <option :value="row . id" v-for="row in items"
                                                                v-html="row.name">
                                                        </option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="margin-top: 30px;">
                                                <button type="button" class="btn btn-info btn-block"
                                                        @click="data_input" :disabled="isDisabled">Add
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
                                                            </td>
                                                            <td>
                                                                @{{ row.group }}
                                                            </td>
                                                            <td>
                                                                @{{ row.name }}
                                                                <input type="hidden"
                                                                       :name="'products[' + index + '][coi_id]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.id">

                                                            </td>
                                                            <td>
                                                                @{{ row.uom }}
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" v-model="row.quantity"
                                                                       :name="'products[' + index + '][quantity]'"
                                                                       class="form-control input-sm"
                                                                       @change="itemtotal(row);valid_quantity(row)"
                                                                       required>
                                                            </td>
                                                            <td>
                                                                <input type="number" v-model="row.rate"
                                                                       :name="'products[' + index + '][rate]'"
                                                                       class="form-control input-sm"
                                                                       @change="itemtotal(row)" required readonly>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control input-sm"
                                                                       v-bind:value="itemtotal(row)" readonly>
                                                            </td>
                                                            <td>
{{--                                                                <button type="button" class="btn btn-sm btn-danger"--}}
{{--                                                                        @click="delete_row(row)"><i--}}
{{--                                                                        class="fa fa-trash"></i></button>--}}
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                        <tfoot>
                                                        <tr>
                                                            <td colspan="8" style="background-color: #DDDCDC">

                                                            </td>
                                                        </tr>
                                                        <tr class="text-right">
                                                            <td colspan="4">
                                                                Total
                                                            </td>

                                                            <td>
                                                                @{{ total_quantity? total_quantity:0 }}
                                                                <input type="hidden" class="form-control input-sm"
                                                                       name="total_quantity"
                                                                       v-bind:value="total_quantity" readonly>
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
                            <div class="card-footer" v-if="selected_items.length > 0 && submittable">
                                <button class="float-right btn btn-primary" type="submit"><i
                                        class="fa fa-fw fa-lg fa-check-circle"></i>Submit
                                </button>
                            </div>
                        </div>
                        <div class="card" v-if="rmItems.length > 0">
                            <div class="card-header bg-info">
                                <h3 class="card-title">RM INFO</h3>
                            </div>
                            <div class="card-body">
                                <div class="card-box">
                                    <div id="">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <hr>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead class="bg-secondary">
                                                        <tr>
                                                            <th style="width: 10px">#</th>
                                                            <th>Item</th>
                                                            <th style="width: 50px">Unit</th>
                                                            <th style="width: 50px">Stock</th>
                                                            <th style="width: 180px">Cost Qty</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(row, index) in rmItems" :style="{ backgroundColor: row.in_stock === false ? 'red' : '' }">

                                                            <td>
                                                                @{{ ++index }}
                                                            </td>
                                                            <td>
                                                                @{{ row.rm_name }}
                                                            </td>
                                                            <td>
                                                                @{{ row.unit }}
                                                            </td>
                                                            <td>
                                                                @{{ row.current_stock }}
                                                            </td>
                                                            <td>
                                                                @{{ row.total_qty }}
                                                            </td>
                                                        </tr>
                                                        </tbody>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                        get_all_info_url: "{{ url('fetch-requisition-items-and-recipe') }}",
                    },
                    date: new Date(),
                    serial_no: {{$serial_no}},
                    factory_id: '',
                    store_id: '',
                    batch_id: '',
                    group_id: '',
                    item_id: '',
                    items: [],
                    quantity: 0,
                    rate: '',
                    selected_items: [],
                    pageLoading: false,
                    isDisabled: false,
                    requisition_id: [],
                    rmItems: [],
                    submittable: true,
                    modification: [],
                    rm_store_id:''
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
                    subtotal: function () {
                        return this.selected_items.reduce((total, item) => {
                            const quantity = parseFloat(item.quantity) || 0;
                            const rate = parseFloat(item.rate) || 0;
                            return total + quantity * rate;
                        }, 0)
                    },


                },
                methods: {

                    fetch_items() {

                        let vm = this;
                        let group_id = vm.group_id;
                        if (group_id) {
                            vm.pageLoading = true;
                            axios.get(this.config.get_items_info_by_group_id_url + '/' + group_id).then(function (response) {
                                vm.items = [];
                                vm.item_id = '';
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

                        let vm = this;

                        if (!vm.group_id) {
                            toastr.error('Please Select Group', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        } else {
                            vm.isDisabled = true
                            let item_id = vm.item_id;
                            let exists = vm.selected_items.some(function (field) {
                                return field.id == item_id
                            });

                            if (exists) {
                                toastr.info('Item Already Selected', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                vm.isDisabled = false
                                return
                            } else {
                                if (item_id) {
                                    vm.modification.push({
                                        fg_id: item_id, quantity: 0
                                    })
                                    vm.fetchRequisitionItems()
                                    // vm.pageLoading = true;
                                    // axios.get(this.config.get_item_info_url + '/' + item_id).then(function (response) {
                                    //     let item_info = response.data;
                                    //     // console.log(item_info);
                                    //     vm.selected_items.push({
                                    //         id: item_info.id,
                                    //         group: item_info.parent.name,
                                    //         name: item_info.name,
                                    //         uom: item_info?.unit?.name,
                                    //         rate: item_info.price,
                                    //         quantity: item_info?.quantity,
                                    //     });
                                    //     vm.isDisabled = false
                                    //     vm.pageLoading = false;
                                    //     vm.modification.push({
                                    //         fg_id: item_info.id, quantity: item_info?.quantity ?? 0
                                    //     })
                                    //     vm.fetchRequisitionItems()
                                    //
                                    // }).catch(function (error) {
                                    //     console.log(error)
                                    //     toastr.error('Something went to wrong', {
                                    //         closeButton: true,
                                    //         progressBar: true,
                                    //     });
                                    //     vm.isDisabled = false
                                    //     return false;
                                    //
                                    // });
                                    vm.isDisabled = false
                                } else {
                                    toastr.info('Select Item', {
                                        closeButton: true,
                                        progressBar: true,
                                    });
                                    vm.isDisabled = false
                                    return

                                    vm.pageLoading = true;
                                    axios.get(this.config.get_items_info_by_group_id_url + '/' + vm.group_id).then(function (response) {
                                        //  vm.selected_items = [];
                                        vm.item_id = '';
                                        let items = response.data.products;
                                        for (let key in items) {
                                            let exists = vm.selected_items.some(function (field) {
                                                return field.id == items[key].id
                                            });
                                            if (exists) {
                                                vm.pageLoading = false;
                                                toastr.error('Item Already Selected Fom this group', {
                                                    closeButton: true,
                                                    progressBar: true,
                                                });
                                                vm.isDisabled = false
                                                return
                                            }
                                            vm.selected_items.push(items[key]);
                                        }
                                        vm.pageLoading = false;
                                        vm.isDisabled = false
                                    }).catch(function (error) {
                                        toastr.error('Something went to wrong', {
                                            closeButton: true,
                                            progressBar: true,
                                        });
                                        vm.isDisabled = false
                                        return false;
                                    });

                                }
                            }
                        }
                    },

                    data_edit() {
                        toastr.error('Under Construction-------', {
                            closeButton: true,
                            progressBar: true,
                        });

                    },
                    delete_row: function (row) {
                        this.selected_items.splice(this.selected_items.indexOf(row), 1);
                    },
                    itemtotal: function (index) {
                        const quantity = parseFloat(index.quantity) || 0;
                        const rate = parseFloat(index.rate) || 0;
                        return quantity * rate;
                    },
                    valid_quantity: function (index) {
                        const vm= this
                        if (index.quantity < 0) {
                            toastr.error('Quantity Negative not Allow', {
                                closeButton: true,
                                progressBar: true,
                            });
                            index.quantity = 0;
                        }
                        vm.modification.push({
                           fg_id: index.id, quantity: index.quantity
                        })
                        this.fetchRequisitionItems()
                    },
                    fetchRequisitionItems() {
                        const vm = this;
                        if (!vm.rm_store_id) {
                            vm.requisition_id = []
                            toastr.error('Select RM Store', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return
                        }

                        if(vm.requisition_id.length < 1){
                            toastr.info('Select Requisition', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return
                        }
                        vm.selected_items = []
                        vm.pageLoading = true;
                        axios.get(this.config.get_all_info_url, {
                            params: {
                                requisition_ids: vm.requisition_id,
                                store_id: vm.rm_store_id,
                                modification: vm.modification
                            }
                        }).then(function (response) {
                            const details = response.data
                            var fgItems = details.fg
                            var rmItems = details.rm
                            vm.rmItems = rmItems
                            vm.submittable = details.submittable
                            Object.values(fgItems).forEach(item_info => {
                                vm.selected_items.push({
                                    id: item_info.fg_id,
                                    group: item_info.group,
                                    name: item_info.name,
                                    uom: item_info.uom,
                                    rate: item_info.rate,
                                    quantity: item_info.total_qty,
                                });
                            });

                            vm.pageLoading = false;
                        }).catch(function (error) {
                            console.log(error)
                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });

                            return false;

                        });
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
