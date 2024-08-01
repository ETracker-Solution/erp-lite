@extends('layouts.app')
@section('title')
    Supplier Opening Balance
@endsection
@section('content')
    @php
        $links = [
       'Home'=>route('dashboard'),
       'Master Data'=>'',
       'Opening Balance'=>'',
       'Supplier OB'=>'',
        ]
    @endphp
    <x-breadcrumb title='Supplier OB' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <span v-if="pageLoading" class="categoryLoader">
                            <img src="{{ asset('loading.gif') }}" alt="loading">
                        </span>
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">Supplier</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <label for="item_id">SOB ID</label>
                                    <input type="text" name="" id="" v-model="next_id" disabled class="form-control">
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">Date</label>
                                        <vuejs-datepicker v-model="date" name="date"
                                                          placeholder="Select date"></vuejs-datepicker>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="item_id">Suppliers</label>
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
                                        <label for="rate">Amount</label>
                                        <input type="number" name="amount" id="amount" v-model="amount"
                                               class="form-control">
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
                        <div class="card-footer" v-if="item_id && amount && date">
                            <div class="text-center">
                                <button class="btn btn-sm btn-secondary" type="button" @click="update_balance"
                                        v-if="isEditMode"><i
                                        class="fa fa-save"></i>Update
                                </button>
                                <button class="btn btn-sm btn-danger" type="button" @click="delete_balance" v-if="isEditMode">
                                    <i
                                        class="fa fa-trash"></i>Delete
                                </button>
                                <button class="btn btn-sm btn-info" type="button" @click="store_balance" v-if="!isEditMode">
                                    <i
                                        class="fa fa-check-circle"></i>Submit
                                </button>
                                <button class="btn btn-sm btn-primary" type="button" @click="backAsStarting(true)"><i
                                        class="fa fa-retweet"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">Supplier Opening Balance List</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>SOB ID</th>
                                            <th>Date</th>
                                            <th>Supplier Name</th>
                                            <th>Amount</th>
                                            <th>Remarks</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(row , index) in balances" @click="makeEditable(row)">
                                            <td>@{{ row.uid }}</td>
                                            <td>@{{ row.date }}</td>
                                            <td>@{{ row.supplier.name }}</td>
                                            <td>@{{ row.amount }}</td>
                                            <td>@{{ row.remarks }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <nav aria-label="...">
                                        <ul class="pagination">
                                            <li :class="!previousPageUrl ? 'page-item disabled' : 'page-item'">
                                                <span class="page-link" @click="handlePageChange(--currentPage)">Previous</span>
                                            </li>
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
                        SOBUrl: "{{ url('supplier-opening-balances') }}",
                        initial_info_url: "{{ url('supplier-opening-balances-initial-info') }}",
                        get_balances_url: "{{ url('supplier-opening-balances-list') }}",
                    },
                    next_id: "",
                    date: new Date(),
                    item_id: '',
                    items: [],
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
                    amount: 0,


                },
                components: {
                    vuejsDatepicker,
                },
                methods: {
                    store_balance() {
                        const vm = this;
                        if (!vm.item_id) {
                            toastr.error('Please Select Account', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        } else {
                            const slug = vm.item_id;
                            if (slug) {
                                vm.pageLoading = true;
                                axios.post(this.config.SOBUrl, {
                                    date: vm.date,
                                    item_id: vm.item_id,
                                    remarks: vm.remarks,
                                    amount: vm.amount,
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
                        vm.item_id = row.supplier_id
                        vm.amount = row.amount
                        vm.remarks = row.remarks
                    },
                    backAsStarting(reset=false) {
                        var vm = this;
                        vm.isEditMode = false
                        vm.date = new Date()
                        vm.item_id = ''
                        vm.items = []
                        vm.pageLoading = false
                        vm.remarks = ''
                        vm.amount = 0
                        vm.editableItem = ''
                        if(reset){
                            vm.get_initial_data()
                        }
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
                                axios.put(this.config.SOBUrl + '/' + slug, {
                                    date: vm.date,
                                    item_id: vm.item_id,
                                    remarks: vm.remarks,
                                    amount: vm.amount,
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
                                        axios.delete(this.config.SOBUrl + '/' + slug)
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
                            vm.items = response.data.suppliers;
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
