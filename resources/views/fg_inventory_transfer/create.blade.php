@extends('layouts.app')
@section('title')
    FG Inventory Transfer
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'FG Inventory Transfer list'=>''
        ]
    @endphp
    <x-breadcrumb title='FG Inventory Transfer Entry' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <span v-if="pageLoading" class="pageLoader">
                    <img src="{{ asset('loading.gif') }}" alt="loading">
                </span>
                <div class="col-lg-12 col-md-12">
                    <form action="{{ route('fg-inventory-transfers.store') }}" method="POST" class="">
                        @csrf
                        <input type="hidden" name="submission_token"
                               value="{{ session()->get('submission_token') ?? Str::random(40) }}">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">FG Inventory Transfer(FGIT) Entry </h3>
                                <div class="card-tools">
                                    <a class="btn btn-sm btn-primary"
                                       href="{{route('fg-inventory-transfers.index')}}">
                                        <i class="fa fa-list" aria-hidden="true"></i> &nbsp;See List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="card-box">
                                    <hr>
                                    <div id="">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="serial_no">FGIT No</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control input-sm"
                                                               value="{{$serial_no}}" name="serial_no"
                                                               id="serial_no">
                                                        {{-- <span class="input-group-append">
                                                                    <button type="button"
                                                                            class="btn btn-info btn-flat">Search</button>
                                                                </span> --}}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="from_store_id">From Store</label>
                                                    <select name="from_store_id" id="from_store_id"
                                                            class="form-control bSelect" v-model="from_store_id"
                                                            required>
                                                        <option value="">Select Store</option>
                                                        @foreach($stores as $row)
                                                            <option
                                                                value="{{ $row->id }}">{{ $row->id }}
                                                                - {{ $row->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="to_store_id">To Store</label>
                                                    <select name="to_store_id" id="to_store_id"
                                                            class="form-control bSelect"
                                                            v-model="to_store_id" required>
                                                        <option value="">Select One</option>
                                                        @foreach($to_stores as $row)
                                                            <option
                                                                value="{{ $row->id }}">{{ $row->id }}
                                                                - {{ $row->name }}</option>
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
                                                    <label for="date">Date</label>
                                                    <vuejs-datepicker v-model="date" name="date"
                                                                      placeholder="Select date"
                                                                      format="yyyy-MM-dd"></vuejs-datepicker>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
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
                        </div>
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">FGT Item Information</h3>
                                <div class="card-tools">

                                </div>
                            </div>

                            <div class="card-body">
                                <div class="card-box">
                                    <div id="">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label for="group_id" class="control-label">Group</label>
                                                    <select class="form-control bSelect" name="group_id"
                                                            v-model="group_id" @change="fetch_item">
                                                        <option value="">Select One</option>
                                                        @foreach ($groups as $category)
                                                            <option
                                                                value="{{ $category->id }}">{{ $category->name }}</option>
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

                                                        <option :value="row.id" v-for="row in products"
                                                                v-html="row.name">
                                                        </option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="margin-top: 26px;">
                                                <button type="button" class="btn btn-info btn-block"
                                                        @click="data_input" :disabled="isDisabled">Add
                                                </button>
                                            </div>

                                            <br>
                                            <br>
                                            <br>
                                            <br>

                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                                <hr>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead class="bg-secondary">
                                                        <tr>
                                                            <th style="width: 3%">#</th>
                                                            <th style="width: 17%">Group</th>
                                                            <th style="width: 31%">Item</th>
                                                            <th style="width: 5%">Unit</th>
                                                            <th style="width: 8%">Balance Qty</th>
                                                            <th style="width: 8%">Selling Price</th>
                                                            <th style="width: 15%">Quantity</th>
                                                            <th style="width: 8%;vertical-align: middle">Value</th>
                                                            <th style="width: 5%"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(row, index) in items">
                                                            <td>
                                                                @{{ ++index }}
                                                            </td>
                                                            <td>
                                                                @{{ row.group }}
                                                            </td>
                                                            <td>
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][coi_id]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.coi_id">
                                                                @{{ row.name }}
                                                            </td>
                                                            <td>
                                                                @{{ row.unit }}
                                                            </td>
                                                            <td class="text-right">
                                                                @{{ row.balance_qty }}
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][balance_qty]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.balance_qty" readonly>
                                                            </td>
                                                            <td class="text-right">
                                                                @{{ row.price }}
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][rate]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.price" readonly>
                                                            </td>
                                                            <td class="text-right">
                                                                <input type="number" v-model="row.quantity"
                                                                       :name="'products['+index+'][quantity]'"
                                                                       class="form-control input-sm"
                                                                       @change="valid(row);itemtotal(row)" required>
                                                            </td>
                                                            <td class="text-right">
                                                                @{{ itemtotal(row) }}
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger"
                                                                        @click="delete_row(row)"><i
                                                                        class="fa fa-trash"></i></button>
                                                            </td>
                                                        </tr>

                                                        </tbody>
                                                        <tfoot>
                                                        <tr>
                                                            <td colspan="9" style="background-color: #DDDCDC"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="6">

                                                            </td>
                                                            <td class="text-right">
                                                                SubTotal
                                                            </td>
                                                            <td class="text-right">
                                                                @{{subtotal}}
                                                            </td>
                                                            <td class="text-right">

                                                            </td>

                                                        </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right" v-if="items.length > 0">
                                    <button class="float-right btn btn-primary" type="submit"><i
                                            class="fa fa-fw fa-lg fa-check-circle"></i>Submit
                                    </button>
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
                        get_item_info_url: "{{ url('fetch-item-by-id-for-sale') }}",
                    },

                    date: new Date(),
                    from_store_id: '',
                    to_store_id: '',
                    group_id: '',
                    item_id: '',
                    products: [],
                    items: [],
                    pageLoading: false,
                    isDisabled: false

                },
                components: {
                    vuejsDatepicker
                },
                computed: {

                    subtotal: function () {
                        return this.items.reduce((total, item) => {
                            return total + (item.quantity * item.price)
                        }, 0)
                    },

                },
                methods: {

                    fetch_item() {

                        let vm = this;

                        let slug = vm.group_id;
                        //    alert(slug);
                        if (slug) {
                            axios.get(this.config.get_items_info_by_group_id_url + '/' + slug).then(function (response) {

                                vm.products = response.data.products;
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

                        if (!vm.from_store_id) {
                            toastr.error('Enter Store', {
                                closeButton: true,
                                progressBar: true,
                            });

                            return false;

                        }
                        if (!vm.item_id) {
                            toastr.error('Enter product', {
                                closeButton: true,
                                progressBar: true,
                            });

                            return false;

                        } else {
                            vm.isDisabled = true
                            let slug = vm.item_id;
                            let exists = vm.items.some(function (field) {
                                return field.coi_id == slug
                            });

                            if (exists) {
                                toastr.info('Item Already Selected', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                vm.isDisabled = false
                                return
                            } else {
                                if (slug) {
                                    axios.get(this.config.get_item_info_url + '/' + slug, {
                                        params: {
                                            store_id: vm.from_store_id
                                        }
                                    }).then(function (response) {
                                        let item_info = response.data;
                                        vm.items.push({
                                            coi_id: item_info.coi_id,
                                            group: item_info.group,
                                            name: item_info.name,
                                            unit: item_info.unit,
                                            balance_qty: item_info.balance_qty,
                                            price: item_info.price,
                                            quantity: '',
                                        });

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

                    delete_row: function (row) {
                        this.items.splice(this.items.indexOf(row), 1);
                    },
                    itemtotal: function (row) {
                        console.log(row);
                        return row.quantity * row.price;

                    },
                    valid: function (index) {
                        if (index.quantity <= 0) {
                            toastr.error('Quantity 0 or Negative not Allow', {
                                closeButton: true,
                                progressBar: true,
                            });
                            index.quantity = '';
                        }
                        if (index.balance_qty < index.quantity) {
                            index.quantity = index.balance_qty;
                        }
                    },

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
