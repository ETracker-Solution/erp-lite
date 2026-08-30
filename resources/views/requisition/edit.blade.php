@extends('layouts.app')
@section('title')
    FG Requisition
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'FG Requisition list'=>''
        ]
    @endphp
    <x-breadcrumb title='FG Requisition Entry Edit' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <div v-if="pageLoading" class="pageLoader" role="status" aria-live="polite">
                    <span class="spinner-border spinner-border-sm mr-2" aria-hidden="true"></span>
                    Loading requisition…
                </div>
                <div class="col-lg-10 offset-lg-1 col-md-12">
                    <form action="{{ route('requisitions.update',$requisition->id) }}" method="POST" class="prevent-enter-submit">
                        @csrf
                        @method('PUT')
                        <div class="card card-info">
                            <div class="card-header bg-info">
                                <h3 class="card-title mb-0">Edit Finished Goods Requisition</h3>
                                <div class="card-tools">
                                    <a href="{{route('requisitions.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list" aria-hidden="true"></i> &nbsp;FG Requisition List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    Update the route or quantities below, then save the requisition.
                                </p>
                                <div class="card-box">
                                    <div id="">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="requisition_no">FGR No</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control input-sm"
                                                               value="" name="serial_no"
                                                               id="serial_no" v-model="serial_no">
                                                        <span class="input-group-append">
                                                                    <button type="button" class="btn btn-info btn-flat">Search</button>
                                                        </span>
                                                    </div>
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
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="from_store_id">From Store</label>
                                                    <select name="from_store_id" id="from_store_id"
                                                            class="form-control bSelect"
                                                            v-model="from_store_id" required>
                                                        <option value="">Select One</option>
                                                        @foreach($from_stores as $row)
                                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="to_store_id">To Store</label>
                                                    <select name="to_store_id" id="to_store_id"
                                                            class="form-control bSelect"
                                                            v-model="to_store_id" required>
                                                        <option value="">Select One</option>
                                                        @foreach($to_stores as $row)
                                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="reference_no">Reference No</label>
                                                    <input type="text" class="form-control input-sm"
                                                           placeholder="Enter Reference No"
                                                           value="{{old('reference_no')}}" name="reference_no"
                                                           v-model="reference_no">
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="remark">Remark</label>
                                                    <textarea class="form-control" name="remark" rows="1"
                                                              placeholder="Enter Remark" v-model="remark"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-info">
                            <div class="card-header bg-info">
                                <h3 class="card-title mb-0">Requisition Items</h3>
                                <div class="card-tools">
                                    <a href="{{route('requisitions.index')}}">

                                    </a>
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
                                                    <select name="item_id" id="item_id" class="form-control bSelect"
                                                            v-model="item_id">
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
                                                    <table class="table table-bordered table-sm mb-0">
                                                        <thead class="bg-secondary">
                                                        <tr>
                                                            <th style="width: 5%">#</th>
                                                            <th style="width: 20%">Group</th>
                                                            <th style="width:35%">Item</th>
                                                            <th style="width: 6%">Unit</th>
                                                            <th style="width: 8%;vertical-align: middle">Quantity</th>
                                                            <th style="width: 6%"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-if="items.length === 0 && !pageLoading">
                                                            <td colspan="6" class="text-center text-muted py-4">
                                                                Add at least one requisition item.
                                                            </td>
                                                        </tr>
                                                        <tr v-for="(row, index) in items">
                                                            <td>
                                                                @{{ index + 1 }}
                                                            </td>
                                                            <td style="vertical-align: middle">
                                                                @{{ row.group }}
                                                            </td>
                                                            <td style="vertical-align: middle">
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][coi_id]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.coi_id">
                                                                @{{ row.name }}
                                                            </td>
                                                            <td style="vertical-align: middle">
                                                                @{{ row.unit }}
                                                            </td>
                                                            <td style="vertical-align: middle" class="text-right">
                                                                <input type="number" v-model="row.requisition_quantity"
                                                                       :name="'products['+index+'][quantity]'"
                                                                       class="form-control input-sm"
                                                                       @change="valid(row)" required>
                                                            </td>
                                                            <td style="vertical-align: middle">
                                                                <button type="button" class="btn btn-danger"
                                                                        @click="delete_row(row)"><i
                                                                        class="fa fa-trash"></i></button>
                                                            </td>
                                                        </tr>

                                                        </tbody>
                                                        <tfoot>
                                                        <tr>
                                                            <td colspan="6" style="background-color: #DDDCDC">

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" class="text-right">
                                                                Total Quantity
                                                            </td>
                                                            <td class="text-right">
                                                                @{{total_quantity}}
                                                                <input type="hidden" :name="'total_quantity'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="total_quantity" readonly>
                                                                <input type="hidden" :name="'total_item'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="items.length" readonly>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer erp-save-bar">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right" v-if="items.length > 0">
                                    <button class="float-right btn btn-primary" type="submit" :disabled="isDisabled || pageLoading">
                                        <span v-if="pageLoading" class="spinner-border spinner-border-sm mr-1"
                                              role="status" aria-hidden="true"></span>
                                        <i v-else class="fa fa-fw fa-lg fa-check-circle"></i>
                                        @{{ pageLoading ? 'Loading…' : 'Save Changes' }}
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
@push('style')
    <style>
        .pageLoader {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: .65rem 1rem;
            color: #fff;
            background: rgba(23, 43, 77, .95);
            border-radius: .25rem;
            box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .2);
            z-index: 1050;
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

                        get_old_items_data: "{{ url('fetch-requisition-by-id') }}",
                    },
                    requisition_id: {{$requisition->id}},
                    date: '',
                    reference_no: '',
                    remark: '',
                    serial_no: {{$requisition->uid}},
                    from_store_id: {{$requisition->from_store_id}},
                    to_store_id: {{$requisition->to_store_id}},
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

                    total_quantity: function () {
                        return this.items.reduce((total, item) => {
                            return total + parseFloat(item.requisition_quantity ? item.requisition_quantity : 0)
                        }, 0)
                    },

                },
                methods: {

                    fetch_item() {

                        var vm = this;

                        var slug = vm.group_id;
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
                                    axios.get(this.config.get_item_info_url + '/' + slug).then(function (response) {

                                        let product_details = response.data;
                                        console.log(product_details);
                                        vm.items.push({
                                            coi_id: product_details.coi_id,
                                            group: product_details.group,
                                            name: product_details.name,
                                            unit: product_details.unit,
                                            balance_qty: product_details.balance_qty,
                                            price: product_details.price,
                                            quantity: product_details.quantity,

                                        });

                                        vm.item_id = '';
                                        vm.group_id = '';
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
                    load_old() {
                        var vm = this;
                        var slug = vm.requisition_id;
                        vm.pageLoading = true;
                        axios.get(this.config.get_old_items_data + '/' + slug).then(function (response) {
                            var item = response.data.items;
                            for (key in item) {
                                vm.items.push(item[key]);
                            }
                            ;
                            vm.store_id = response.data.store_id;
                            vm.date = response.data.date;
                            vm.reference_no = response.data.reference_no;
                            vm.remark = response.data.remark;
                            vm.pageLoading = false;
                        })

                    },
                    valid: function (index) {

                        console.log(index.requisition_quantity);
                        if (index.requisition_quantity <= 0) {
                            toastr.error('Quantity 0 or Negative not Allow', {
                                closeButton: true,
                                progressBar: true,
                            });
                            index.requisition_quantity = '';
                        }
                    }
                },
                beforeMount() {
                    this.load_old();
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
