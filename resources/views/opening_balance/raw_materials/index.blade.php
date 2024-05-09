@extends('layouts.app')
@section('title')
    RM Opening Balance
@endsection
@section('content')
    @php
        $links = [
       'Home'=>route('dashboard'),
       'Data Admin Module'=>'',
       'Opening Balance'=>'',
       'Raw Materials'=>'',
        ]
    @endphp
    <x-breadcrumb title='Raw Materials' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <span v-if="pageLoading" class="categoryLoader">
                            <img src="{{ asset('loading.gif') }}" alt="loading">
                        </span>
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">Raw Material Opening Balance Details</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <label for="item_id">RMOB ID</label>
                                    <input type="text" name="" id="" v-model="serial_id" disabled class="form-control">
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">Date</label>
                                        <vuejs-datepicker v-model="date" name="date"
                                                          placeholder="Select date"></vuejs-datepicker>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="group_id">Group</label>
                                        <select name="group_id" id="group_id" @change="fetch_product"
                                                class="form-control" v-model="group_id">
                                            <option value="">Select a Group</option>
                                            @foreach($groups as $group)
                                                <option value="{{$group->id}}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="item_id">Item</label>
                                        <select name="item_id" id="item_id"
                                                class="form-control bSelect" v-model="item_id"
                                                @change="get_product_info">
                                            <option value="">Select one</option>
                                            <option :value="row.id" v-for="row in items"
                                            >@{{ row.id + ' - ' + row.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="item_id">Unit of Measurement</label>
                                        <input type="text" name="" id="" v-model="unit" disabled class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" name="quantity" id="quantity" v-model="quantity"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="rate">Rate</label>
                                        <input type="number" name="rate" id="rate" v-model="rate" class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="rate">Value</label>
                                        <input type="number" name="amount" id="amount" v-model="amount"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="store_id">Storage Location</label>
                                        <select name="store_id" id="store_id" class="form-control" v-model="store_id">
                                            <option value="">Select a Store</option>
                                            @foreach($stores as $store)
                                                <option value="{{$store->id}}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-9">
                                    <div class="form-group">
                                        <label for="store_id">Remarks</label>
                                        <textarea name="" id="" cols="50" rows="1" v-model="remarks"
                                                  class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer" v-if="item_id && quantity && rate && store_id">
                            <div class="text-center">
                                <button class="btn btn-primary" type="button" @click="store_balance"><i
                                        class="fa fa-fw fa-lg fa-check-circle"></i>Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">Raw Materials Opening Balance List</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>ROM ID</th>
                                            <th>Date</th>
                                            <th>Group Name</th>
                                            <th>Item Name</th>
                                            <th>UoM</th>
                                            <th>QTY</th>
                                            <th>Rate</th>
                                            <th>Value</th>
                                            <th>Store</th>
                                            <th>Remarks</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(row , index) in balances" @click="makeEditable(row)">
                                            <td>@{{ row.id }}</td>
                                            <td>@{{ row.date }}</td>
                                            <td>@{{ row.group }}</td>
                                            <td>@{{ row.item_name }}</td>
                                            <td>@{{ row.unit }}</td>
                                            <td>@{{ row.qty }}</td>
                                            <td>@{{ row.rate }}</td>
                                            <td>@{{ row.value }}</td>
                                            <td>@{{ row.store }}</td>
                                            <td>@{{ row.remarks }}</td>
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
    </section>
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
                        addRMOBUrl: "{{ url('raw-materials-opening-balances') }}",
                        updateRMOBUrl: "{{ url('raw-materials-opening-balances') }}",
                        get_items_info_by_group_id_url: "{{ url('fetch-items-by-group-id') }}",
                        get_item_info_url: "{{ url('fetch-item-info') }}",
                        get_balances_url: "{{ url('raw-materials-opening-balances-list') }}",
                    },
                    serial_id: "{{ $serial_no }}",
                    date: new Date(),
                    group_id: '',
                    item_id: '',
                    unit: '',
                    store_id: '',
                    items: [],
                    quantity: '',
                    rate: '',
                    pageLoading: false,
                    remarks: '',
                    balances: [],
                    nextPageUrl: '',
                    previousPageUrl: '',
                    lastPage: 1,
                    currentPage: 1,
                    total: '',
                },
                components: {
                    vuejsDatepicker
                },
                computed: {
                    amount: function () {
                        return this.quantity * this.rate
                    }
                },
                methods: {
                    fetch_product() {
                        var vm = this;
                        var slug = vm.group_id;
                        if (slug) {
                            vm.pageLoading = true;
                            axios.get(this.config.get_items_info_by_group_id_url + '/' + slug).then(function (response) {
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
                    get_product_info() {
                        const vm = this;
                        if (!vm.item_id) {
                            toastr.error('Please Select Item', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        } else {
                            const slug = vm.item_id;
                            if (slug) {
                                vm.pageLoading = true;
                                axios.get(this.config.get_item_info_url + '/' + slug).then(function (response) {
                                    let item_info = response.data;
                                    vm.unit = item_info.unit ? item_info.unit.name : '';
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
                    store_balance() {
                        const vm = this;
                        if (!vm.item_id) {
                            toastr.error('Please Select Item', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        } else {
                            const slug = vm.item_id;
                            if (slug) {
                                vm.pageLoading = true;
                                axios.post(this.config.addRMOBUrl, {
                                    date: vm.date,
                                    qty: vm.quantity,
                                    rate: vm.rate,
                                    store_id: vm.store_id,
                                    item_id: vm.item_id,
                                    remarks: vm.remarks,
                                })
                                    .then(function (response) {
                                        vm.date = new Date()
                                        vm.group_id = ''
                                        vm.item_id = ''
                                        vm.unit = ''
                                        vm.store_id = ''
                                        vm.items = []
                                        vm.quantity = ''
                                        vm.rate = ''
                                        vm.pageLoading = false
                                        vm.remarks = ''

                                        toastr.success(response.message, {
                                            closeButton: true,
                                            progressBar: true,
                                        });

                                        window.location.reload()
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
                    get_balances(){
                        var vm = this;
                        vm.pageLoading = true;
                        axios.get(this.config.get_balances_url).then(function (response) {
                            vm.balances = response.data.items.data;
                            vm.nextPageUrl = response.data.items.links.next
                            vm.previousPageUrl = response.data.items.links.prev
                            vm.lastPage = response.data.items.meta.last_page_number
                            vm.currentPage = response.data.items.meta.current_page
                            vm.total = response.data.items.meta.total
                            vm.pageLoading = false;
                        }).catch(function (error) {
                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });
                    },
                    makeEditable(row){
                        console.log(row)
                        var vm = this;
                        vm.serial_id = row.serial_no
                        vm.date = row.date
                        vm.group_id = row.group_id
                        vm.item_id = row.item_id
                        vm.unit = row.unit
                        vm.store_id = row.store_id,
                        vm.quantity = row.qty
                        vm.rate = row.rate
                        vm.remarks = row.remarks
                    }
                },

                updated() {
                    $('.bSelect').selectpicker('refresh');
                },
                mounted(){
                    this.get_balances()
                }
            });
            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });
        });
    </script>
@endpush
