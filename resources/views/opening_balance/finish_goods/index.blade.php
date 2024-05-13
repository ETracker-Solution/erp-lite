@extends('layouts.app')
@section('title')
    FG Opening Balance
@endsection
@section('content')
    @php
        $links = [
       'Home'=>route('dashboard'),
       'Data Admin Module'=>'',
       'Opening Balance'=>'',
       'Finish Goods'=>'',
        ]
    @endphp
    <x-breadcrumb title='Finish Goods' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <span v-if="pageLoading" class="categoryLoader">
                            <img src="{{ asset('loading.gif') }}" alt="loading">
                        </span>
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">Finish Goods Opening Balance Details</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <label for="item_id">FGOB ID</label>
                                    <input type="text" name="" id="" v-model="next_id" disabled class="form-control">
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
                                            <option :value="row.id" v-for="row in groups"
                                            >@{{ row.id + ' - ' + row.name }}
                                            </option>
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
                                            <option :value="row.id" v-for="row in stores"
                                            >@{{ row.id + ' - ' + row.name }}
                                            </option>
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
                                <button class="btn btn-sm btn-secondary" type="button" @click="update_balance"
                                        v-if="isEditMode"><i
                                        class="fa fa-save"></i>Update
                                </button>
                                <button class="btn btn-sm btn-danger" type="button" @click="delete_balance" v-if="isEditMode">
                                    <i
                                        class="fa fa-trash"></i>Delete
                                </button>
                                <button class="btn btn-sm btn-primary" type="button" @click="store_balance" v-if="!isEditMode">
                                    <i
                                        class="fa fa-check-circle"></i>Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">Finish Goods Opening Balance List</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>FG ID</th>
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
                                            <td>@{{ row.uid }}</td>
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
                                    <nav aria-label="...">
                                        <ul class="pagination">
                                            <li :class="!previousPageUrl ? 'page-item disabled' : 'page-item'">
                                                <span class="page-link" @click="handlePageChange(--currentPage)">Previous</span>
                                            </li>
{{--                                            <li class="page-item"><a class="page-link" href="#">1</a></li>--}}
{{--                                            <li class="page-item active" aria-current="page">--}}
{{--                                                <a class="page-link" href="#">2</a>--}}
{{--                                            </li>--}}
{{--                                            <li class="page-item"><a class="page-link" href="#">3</a></li>--}}
                                            <li :class="!nextPageUrl ? 'page-item disabled' : 'page-item'">
                                                <span class="page-link"  @click="handlePageChange(++currentPage)">Next</span>
                                            </li>
                                        </ul>
                                    </nav>
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
                        FGOBUrl: "{{ url('finish-goods-opening-balances') }}",
                        initial_info_url: "{{ url('finish-goods-opening-balances-initial-info') }}",
                        get_items_info_by_group_id_url: "{{ url('fetch-items-by-group-id') }}",
                        get_item_info_url: "{{ url('fetch-item-info') }}",
                        get_balances_url: "{{ url('finish-goods-opening-balances-list') }}",
                    },
                    next_id: "",
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
                    perPage: 10,
                    total: '',
                    isEditMode: false,
                    editableItem: '',
                    groups: [],
                    stores:[]

                },
                components: {
                    vuejsDatepicker,
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
                                axios.post(this.config.FGOBUrl, {
                                    date: vm.date,
                                    qty: vm.quantity,
                                    rate: vm.rate,
                                    store_id: vm.store_id,
                                    item_id: vm.item_id,
                                    remarks: vm.remarks,
                                })
                                    .then(function (response) {
                                        if(response.data.success){
                                            vm.get_initial_data()
                                            toastr.success(response.data.message, {
                                                closeButton: true,
                                                progressBar: true,
                                            });
                                        }else{
                                            toastr.error(response.data.message, {
                                                closeButton: true,
                                                progressBar: true,
                                            });
                                            vm.pageLoading = false;
                                        }

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
                    get_balances() {
                        var vm = this;
                        vm.pageLoading = true;
                        axios.get(this.config.get_balances_url,{
                            params: {
                                page: vm.currentPage,
                            },
                        }).then(function (response) {
                            vm.balances = response.data.items.data;
                            vm.nextPageUrl = response.data.items.links.next
                            vm.previousPageUrl = response.data.items.links.prev
                            vm.lastPage = response.data.items.meta.last_page_number
                            vm.currentPage = response.data.items.meta.current_page
                            vm.total = response.data.items.meta.total
                            vm.pageLoading = false;
                            vm.backAsStarting()
                        }).catch(function (error) {
                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });
                    },
                    makeEditable(row) {
                        var vm = this;
                        vm.next_id = row.uid
                        vm.isEditMode = true
                        vm.editableItem = row.id
                        vm.date = row.date
                        vm.group_id = row.group_id
                        vm.item_id = row.item_id
                        vm.unit = row.unit
                        vm.store_id = row.store_id
                        vm.quantity = row.qty
                        vm.rate = row.rate
                        vm.remarks = row.remarks
                    },
                    backAsStarting() {
                        var vm = this;
                        vm.isEditMode = false
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
                        vm.editableItem = ''
                    },
                    update_balance() {
                        const vm = this;
                        if (!vm.editableItem) {
                            toastr.error('Please Select Item', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        } else {
                            const slug = vm.editableItem;
                            if (slug) {
                                vm.pageLoading = true;
                                axios.put(this.config.FGOBUrl + '/' + slug, {
                                    date: vm.date,
                                    qty: vm.quantity,
                                    rate: vm.rate,
                                    store_id: vm.store_id,
                                    item_id: vm.item_id,
                                    remarks: vm.remarks,
                                })
                                    .then(function (response) {
                                        if(response.data.success){
                                            vm.get_initial_data()
                                            toastr.success(response.data.message, {
                                                closeButton: true,
                                                progressBar: true,
                                            });
                                        }else{
                                            toastr.error(response.data.message, {
                                                closeButton: true,
                                                progressBar: true,
                                            });
                                            vm.pageLoading = false;
                                        }
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
                    delete_balance() {
                        const vm = this;
                        if (!vm.editableItem) {
                            toastr.error('Please Select Item', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        } else {
                            Swal.fire({
                                title: "Are You Sure!",
                                text: "Update this Item!",
                                icon: "question",
                                showCancelButton: true,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Yes, update it!"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const slug = vm.editableItem;
                                    if (slug) {
                                        vm.pageLoading = true;
                                        axios.delete(this.config.FGOBUrl + '/' + slug)
                                            .then(function (response) {
                                                if(response.data.success){
                                                    vm.get_initial_data()
                                                    toastr.success(response.data.message, {
                                                        closeButton: true,
                                                        progressBar: true,
                                                    });
                                                }else{
                                                    toastr.error(response.data.message, {
                                                        closeButton: true,
                                                        progressBar: true,
                                                    });
                                                    vm.pageLoading = false;
                                                }
                                            }).catch(function (error) {
                                            console.log(error)
                                            toastr.error('Something went to wrong', {
                                                closeButton: true,
                                                progressBar: true,
                                            });
                                            return false;

                                        });
                                    }
                                }
                            });


                        }
                    },
                    get_initial_data() {
                        this.get_balances()
                        var vm = this;
                        vm.pageLoading = true;
                        axios.get(this.config.initial_info_url).then(function (response) {
                            vm.groups = response.data.groups;
                            vm.stores = response.data.stores;
                            vm.next_id = response.data.next_id;
                            vm.pageLoading = false;
                        }).catch(function (error) {
                            toastr.error('Something went to wrong', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });
                    },
                    handlePageChange(){
                        const vm= this
                        vm.get_balances()
                    }
                },

                updated() {
                    $('.bSelect').selectpicker('refresh');
                },
                mounted() {
                    this.get_initial_data()
                }
            });
            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });
        });
    </script>
@endpush
