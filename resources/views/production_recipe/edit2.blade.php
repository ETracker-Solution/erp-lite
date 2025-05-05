@extends('layouts.app')
@section('title', 'Goods Purchase')

@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Pre-define  Recipe Entry'=>''
        ]
    @endphp
    <x-breadcrumb title='Pre-define Recipe' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                   <span v-if="pageLoading" class="categoryLoader">
                       <img src="{{ asset('loading.gif') }}" alt="loading">
                  </span>
                <div class="col-lg-12 col-md-12">
                    <form action="{{ route('production-recipes.update', $recipes[0]->uid) }}" method="POST" class="">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="submission_token"
                               value="{{ session()->get('submission_token') ?? Str::random(40) }}">
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Pre-define Recipe Entry</h3>
                                <div class="card-tools">
                                    <a href="{{route('production-recipes.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-bars"
                                           aria-hidden="true"></i> &nbsp;
                                           See List
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="card-box">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="uid">Recipe No</label>
                                                <input type="text" class="form-control input-sm"
                                                       name="uid" v-model="uid"
                                                       id="uid" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group">
                                                <label for="fg_group_id">ITEM</label>
                                                <select name="fg_group_id" id="fg_group_id"
                                                        class="form-control bSelect">
                                                    <option value="">{{ $recipes[0]->item->name }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Pre-define Recipe Item</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="rm_group_id" class="control-label">RM Group</label>
                                            <select class="form-control bSelect" name="rm_group_id"
                                                    v-model="rm_group_id"
                                                    @change="fetch_product('rm')">
                                                <option value="">Select One</option>
                                                @foreach ($rm_groups as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="rm_item_id">RM Item</label>
                                            <select name="rm_item_id" id="item_id"
                                                    class="form-control bSelect" v-model="rm_item_id">
                                                <option value="">Select one</option>

                                                <option :value="row.id" v-for="row in rm_items"
                                                        v-html="row.name">
                                                </option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12"
                                         style="margin-top: 30px;">
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
                                            <table class="table table-bordered" style="width: 100%">
                                                <thead class="bg-secondary">
                                                <tr>
                                                    <th style="width: 5%">#</th>
                                                    <th style="width: 20%">Group</th>
                                                    <th style="width: 20%">Item</th>
                                                    <th style="width: 20%">Unit</th>
                                                    <th style="width: 20%">Qty</th>
                                                    <th style="width: 15%"></th>
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
                                                               :name="'products['+index+'][coi_id]'"
                                                               class="form-control input-sm"
                                                               v-bind:value="row.id">

                                                    </td>
                                                    <td>
                                                        @{{ row.uom }}
                                                    </td>
                                                    <td>
                                                        <input type="number" v-model="row.quantity"
                                                               :name="'products['+index+'][quantity]'"
                                                               class="form-control input-sm"
                                                               @change="valid_quantity(row)"
                                                               step="0.00001" min="0"
                                                               required>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                                @click="delete_row(row)"><i
                                                                class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="card-footer" v-if="selected_items.length > 0">
                                <button class="float-right btn btn-primary" type="submit"><i
                                        class="fa fa-fw fa-lg fa-check-circle"></i>Submit
                                </button>
                            </div> --}}
                            <div class="card-footer">
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
                    supplier_group_id: '',
                    supplier_id: '',
                    fg_group_id: '',
                    rm_group_id: '',
                    fg_item_id: '',
                    rm_item_id: '',
                    fg_items: [],
                    rm_items: [],
                    suppliers: [],
                    selected_items: @json($selectedItems),
                    pageLoading: false,
                    uid: "{{$uid}}",
                    store_id: '',
                    fg_id: '',
                    isDisabled: false
                },
                components: {
                    vuejsDatepicker
                },
                computed: {

                    subtotal: function () {
                        return this.selected_items.reduce((total, item) => {
                            return total + item.quantity * item.rate
                        }, 0)
                    },
                    net_payable: function () {
                        return this.subtotal + parseFloat(this.vat)
                    },
                },
                methods: {

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

                    fetch_product(type) {

                        var vm = this;

                        var slug = type == 'fg'? vm.fg_group_id : vm.rm_group_id;

                        if (slug) {
                            vm.pageLoading = true;
                            axios.get(this.config.get_items_info_by_group_id_url + '/' + slug).then(function (response) {

                                // vm.selected_items = response.data.products;
                                if(type == 'fg')
                                {
                                    vm.fg_items = response.data.products;
                                }else{
                                    vm.rm_items = response.data.products;
                                }
                                vm.pageLoading = false;
                            }).catch(function (error) {

                                toastr.error('Something went to wrong', {
                                    closeButton: true,
                                    progressBar: true,
                                });

                                return false;

                            });
                        }
                        slug = null;
                    },

                    data_input() {

                        let vm = this;
                        console.log(vm.selected_items)
                        if (!vm.rm_group_id) {
                            toastr.error('Please Select Group', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        }
                        else {
                            vm.isDisabled = true
                            let group_id = vm.rm_group_id;
                            let item_id = vm.rm_item_id;

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
                                    vm.pageLoading = true;
                                    axios.get(this.config.get_item_info_url + '/' + item_id).then(function (response) {
                                        let item_info = response.data;
                                        vm.selected_items.push({
                                            id: item_info.id,
                                            group: item_info.parent.name,
                                            name: item_info.name,
                                            uom: item_info.unit.name,
                                            rate: '',
                                            quantity: '',
                                        });
                                        // vm.item_id = '';
                                        // vm.group_id = '';
                                        vm.pageLoading = false;
                                        vm.isDisabled = false
                                        toastr.success('Added New Item', {
                                            closeButton: true,
                                            progressBar: true,
                                        });

                                    }).catch(function (error) {

                                        toastr.error('Something went to wrong', {
                                            closeButton: true,
                                            progressBar: true,
                                        });
                                        vm.isDisabled = false
                                        return false;

                                    });
                                }
                                else {
                                    vm.pageLoading = true;
                                    axios.get(this.config.get_items_info_by_group_id_url + '/' + vm.group_id).then(function (response) {
                                      //  vm.selected_items=[];
                                        let items = response.data.products;
                                        for (let key in items) {
                                            let exists = vm.selected_items.some(function (field) {
                                                return field.coi_id == items[key].id
                                            });
                                            if (exists){
                                                vm.pageLoading = false;
                                                toastr.error('Item Already Selected Fom this group', {
                                                    closeButton: true,
                                                    progressBar: true,
                                                });
                                                vm.isDisabled = false;
                                                return
                                            }
                                            vm.selected_items.push(items[key]);
                                        }
                                        console.log(vm.selected_items);
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
                        }

                    },
                    delete_row: function (row) {
                        this.selected_items.splice(this.selected_items.indexOf(row), 1);
                    },
                    itemtotal: function (index) {

                        console.log(index.quantity * index.rate);
                        return index.quantity * index.rate;
                    },
                    valid_quantity: function (index) {
                        if (index.quantity <= 0) {
                            toastr.error('Quantity 0 or Negative not Allow', {
                                closeButton: true,
                                progressBar: true,
                            });
                            index.quantity = '';
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
                    getUUID(){
                        const vm = this
                        if (!vm.store_id) {
                            vm.uid = 'Please Select Product First'
                            toastr.error('Please Select valid Product', {
                                closeButton: true,
                                progressBar: true,
                            });
                        }
                        axios.get('/get-uuid/', {
                            params: {
                                fg_id: null,
                                model: "recipe",
                                column:'uid',
                                is_factory: false,
                                is_headOffice: true
                            }
                        }).then((response) => {
                            vm.uid = response.data
                        })
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
