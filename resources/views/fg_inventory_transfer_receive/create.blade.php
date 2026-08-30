@extends('layouts.app')
@section('title')
    FG Inventory Transfer Receive
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'FG Inventory Transfer Receive list'=>''
        ]
    @endphp
    <x-breadcrumb title='FG Inventory Transfer Receive Entry' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row" id="vue_app">
                <div v-if="pageLoading" class="pageLoader" role="status" aria-live="polite">
                    <span class="spinner-border spinner-border-sm mr-2" aria-hidden="true"></span>
                    Loading transfer items…
                </div>
                <div class="col-lg-10 offset-lg-1 col-md-12">
                    <form action="{{ route('fg-transfer-receives.store') }}" method="POST" class="prevent-enter-submit">
                        @csrf
                        <input type="hidden" name="submission_token"
                               value="{{ session()->get('submission_token') ?? Str::random(40) }}">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title mb-0">FG Inventory Transfer Receive</h3>
                                <div class="card-tools">
                                    <a href="{{route('fg-transfer-receives.index')}}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fa fa-list" aria-hidden="true"></i> &nbsp;See List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    Select a pending finished-goods transfer to review and receive its items.
                                </p>
                                <div class="card-box">
                                    <div id="">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="inventory_transfer_id">Inventory Transfer No</label>
                                                    <select name="inventory_transfer_id" id="inventory_transfer_id"
                                                            class="form-control bSelect"
                                                            v-model="inventory_transfer_id" required
                                                            @change="load_old($event)">
                                                        <option value="">Select One</option>
                                                        @foreach($inventory_transfers as $row)
                                                            <option value="{{ $row->id }}"
                                                                {{ (string) ($prefillTransferId ?? '') === (string) $row->id ? 'selected' : '' }}>
                                                                {{ $row->uid ?: ('#'.$row->id) }}
                                                                @if($row->date) — {{ $row->date }}@endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="date">Date</label>
                                                    <input type="date" class="form-control" id="date" name="date"
                                                           v-model="date" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="from_store_id">From Store</label>
                                                    <input type="hidden" name="from_store_id" v-model="from_store_id">
                                                    <select name="from_store_id" id="from_store_id"
                                                            class="form-control bSelect"
                                                            v-model="from_store_id" disabled>
                                                        {{-- <option value="">Select One</option> --}}
                                                        @foreach($from_stores as $row)
                                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="to_store_id">To Store</label>
                                                    <input type="hidden" name="to_store_id" v-model="to_store_id">
                                                    <select name="to_store_id" id="to_store_id"
                                                            class="form-control bSelect"
                                                            v-model="to_store_id" :disabled="true">
                                                        {{-- <option value="">Select One</option> --}}
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
                            <div class="card-header">
                                <h3 class="card-title mb-0">Receive Items</h3>
                            </div>
                            <div class="card-body">
                                <div class="card-box">
                                    <div id="">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <hr>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm mb-0">
                                                        <thead class="bg-secondary">
                                                        <tr>
                                                            <th style="width: 5%">#</th>
                                                            <th style="width: 15%">Group</th>
                                                            <th style="width:25%">Item</th>
                                                            <th style="width: 5%">Unit</th>
                                                            <th style="width: 10%;vertical-align: middle">Transfer
                                                                Quantity
                                                            </th>
                                                            <th style="width: 10%;vertical-align: middle">Receive
                                                                Quantity
                                                            </th>
                                                            <th style="width: 5%"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-if="items.length === 0 && !pageLoading">
                                                            <td colspan="7" class="text-center text-muted py-4">
                                                                Select a transfer to load receive items.
                                                            </td>
                                                        </tr>
                                                        <tr v-for="(row, index) in items" :key="row.coi_id">
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
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][rate]'"
                                                                       class="form-control input-sm"
                                                                       v-bind:value="row.fg_average_rate">
                                                                @{{ row.name }}
                                                            </td>
                                                            <td style="vertical-align: middle">
                                                                @{{ row.unit }}
                                                            </td>
                                                            <td style="vertical-align: middle" class="text-right">
                                                                <input type="number" v-model="row.transfer_quantity"
                                                                       :name="'products['+index+'][transfer_quantity]'"
                                                                       class="form-control input-sm"
                                                                       required readonly>
                                                            </td>

                                                            <td style="vertical-align: middle" class="text-right">
                                                                <input type="number" step="0.01" v-model="row.quantity"
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
                                                            <td colspan="7" style="background-color: #DDDCDC">

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5" class="text-right">
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
                                    <button class="float-right btn btn-primary" type="submit" :disabled="pageLoading">
                                        <span v-if="pageLoading" class="spinner-border spinner-border-sm mr-1"
                                              role="status" aria-hidden="true"></span>
                                        <i v-else class="fa fa-fw fa-lg fa-check-circle"></i>
                                        @{{ pageLoading ? 'Loading…' : 'Submit' }}
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
    <script>
        $(document).ready(function () {

            var vue = new Vue({
                el: '#vue_app',
                data: {
                    config: {

                        get_old_items_data: "{{ url('fetch-inventory-transfer-by-id') }}",
                    },
                    inventory_transfer_id: @json($prefillTransferId ? (string) $prefillTransferId : ''),
                    date: "{{ date('Y-m-d') }}",
                    reference_no: '',
                    remark: '',
                    from_store_id: '',
                    to_store_id: '',
                    group_id: '',
                    item_id: '',
                    products: [],
                    items: [],
                    pageLoading: false,

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
                    load_old(event) {
                        var vm = this;
                        var slug = event && event.target ? event.target.value : vm.inventory_transfer_id;
                        vm.inventory_transfer_id = slug;
                        vm.items = [];
                        if (!slug) {
                            vm.pageLoading = false;
                            vm.from_store_id = '';
                            vm.to_store_id = '';
                            vm.reference_no = '';
                            vm.remark = '';
                            return;
                        }

                        vm.pageLoading = true;
                        axios.get(this.config.get_old_items_data + '/' + slug).then(function (response) {
                            vm.items = Object.values(response.data.items || {});
                            vm.from_store_id = response.data.from_store_id;
                            vm.to_store_id = response.data.to_store_id;
                            vm.date = response.data.date;
                            vm.reference_no = response.data.reference_no;
                            vm.remark = response.data.remark;
                            vm.pageLoading = false;
                        }).catch(function () {
                            vm.pageLoading = false;
                            toastr.error('Failed to load transfer items', {
                                closeButton: true,
                                progressBar: true,
                            });
                        });

                    },
                    valid: function (index) {


                        if (index.transfer_quantity < index.quantity) {
                            console.log('2');
                            index.quantity = index.transfer_quantity;
                        }
                        if (index.quantity <= 0) {
                            console.log('3');
                            index.quantity = '';
                        }
                    }
                },

                mounted() {
                    if (this.inventory_transfer_id) {
                        this.load_old();
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
