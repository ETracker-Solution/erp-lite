@extends('layouts.app')
@section('title')
    RM Requisition
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'RM Requisition Delivery list'=>''
        ]
    @endphp
    <x-breadcrumb title='RM Requisition Delivery Entry' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                  <span v-if="pageLoading" class="pageLoader">
                            <img src="{{ asset('loading.gif') }}" alt="loading">
                        </span>
                <div class="col-lg-12 col-md-12">
                    <form action="{{ route('rm-requisition-deliveries.store') }}" method="POST" class="">
                        @csrf
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">RM Requisition Delivery Entry</h3>
                                <div class="card-tools">
                                    <a href="{{route('rm-requisition-deliveries.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list" aria-hidden="true"></i> &nbsp;RM Requisition Delivery List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="card-box">
                                    <div id="">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="requisition_id">RMR No</label>
                                                    <select name="requisition_id" id="requisition_id"
                                                            class="form-control bSelect"
                                                            v-model="requisition_id" required @change="load_old">
                                                        <option value="">Select One</option>
                                                        @foreach($requisitions as $row)
                                                            <option value="{{ $row->id }}">{{ $row->uid }}</option>
                                                        @endforeach
                                                    </select>
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
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title">RM Requisition Line Item</h3>
                                <div class="card-tools">
                                    <a href="{{route('rm-requisitions.index')}}">

                                    </a>
                                </div>
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
                                                            <th style="width: 5%">#</th>
                                                            <th style="width: 15%">Group</th>
                                                            <th style="width:25%">Item</th>
                                                            <th style="width: 5%">Unit</th>
                                                            <th style="width: 10%;vertical-align: middle">Balance Quantity</th>
                                                            <th style="width: 10%;vertical-align: middle">Requisition Quantity</th>
                                                            <th style="width: 10%;vertical-align: middle">Delivery Quantity</th>
                                                            <th style="width: 5%"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(row, index) in items">
                                                            <td>
                                                                @{{ ++index }}
                                                            </td>
                                                            <td style="vertical-align: middle">
                                                                @{{ row.group }}
                                                            </td>
                                                            <td style="vertical-align: middle">
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][coi_id]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.coi_id">
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][rate]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.rate">
                                                                @{{ row.name }}
                                                            </td>
                                                            <td style="vertical-align: middle">
                                                                @{{ row.unit }}
                                                            </td>
                                                            <td style="vertical-align: middle" class="text-right">
                                                                <input type="number" v-model="row.balance_quantity"
                                                                       :name="'products['+index+'][balance_quantity]'"
                                                                       class="form-control input-sm"
                                                                      required readonly>
                                                            </td> <td style="vertical-align: middle" class="text-right">
                                                                <input type="number" v-model="row.requisition_quantity"
                                                                       :name="'products['+index+'][requisition_quantity]'"
                                                                       class="form-control input-sm"
                                                                     required readonly>
                                                            </td> <td style="vertical-align: middle" class="text-right">
                                                                <input type="number" v-model="row.quantity"
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
                                                            <td colspan="8" style="background-color: #DDDCDC">

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="6" class="text-right">
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

                        get_old_items_data: "{{ url('fetch-requisition-by-id') }}",
                    },
                    requisition_id: '',
                    date: '',
                    reference_no: '',
                    remark: '',
                    serial_no: '',
                    from_store_id: '',
                    to_store_id: '',
                    group_id: '',
                    item_id: '',
                    products: [],
                    items: [],
                    pageLoading: false,

                },
                components: {
                    vuejsDatepicker
                },
                computed: {

                    total_quantity: function () {
                        return this.items.reduce((total, item) => {
                            return total + parseFloat(item.quantity ? item.quantity : 0)
                        }, 0)
                    },

                },
                methods: {
                    delete_row: function (row) {
                        this.items.splice(this.items.indexOf(row), 1);
                    },
                    load_old() {
                        var vm = this;
                        var slug = vm.requisition_id;
                        vm.pageLoading = true;
                        axios.get(this.config.get_old_items_data + '/' + slug+ '/' + vm.from_store_id).then(function (response) {
                            vm.items = [];
                            var item = response.data.items;
                            for (key in item) {
                                vm.items.push(item[key]);
                            }

                            vm.to_store_id = response.data.store_id;
                            vm.date = response.data.date;
                            vm.reference_no = response.data.reference_no;
                            vm.remark = response.data.remark;
                            vm.pageLoading = false;
                        })

                    },

                    valid: function (index) {
                        if(index.quantity > index.balance_quantity ){
                            console.log('1st');
                            index.quantity = index.balance_quantity ;
                        }
                        if(index.requisition_quantity < index.quantity ){
                            console.log('2');
                            index.quantity=index.requisition_quantity;
                        }
                        if(index.quantity <= 0){
                            console.log('3');
                            index.quantity = '';
                        }
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
