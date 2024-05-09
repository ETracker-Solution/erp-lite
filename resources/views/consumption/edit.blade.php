@extends('factory.layouts.app')
@section('title', 'Stock Adjust')
@push('style')
    <link rel="stylesheet" type="text/css"
          href="{{ asset('admin/app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('admin/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('admin/app-assets/css/plugins/forms/pickers/form-pickadate.css') }}">
    <link rel="stylesheet" href="{{ asset('vue-js/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <style>
        .list-hover:hover {
            background-color: #f1f1f1;
        }

        .categoryLoader {
            position: absolute;
            top: 50%;
            right: 40%;
            transform: translate(-50%, -50%);
            color: red;
            z-index: 999;
        }
    </style>
@endpush
@section('content')
    <div class="content-wrapper">
        @php
            $links = [
            'Home' => route('dashboard'),
            'Stock Adjust' => route('factory.stock-adjusts.index'),
            'Stock Adjust create' => '',
            ];
        @endphp
        <x-bread-crumb-component title='Stock Adjust' :links="$links"/>
        <div class="content-body">
            <!-- Basic Inputs start -->
            <section id="basic-input">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{route('factory.stock-adjusts.update',$purchase->id)}}" method="POST" class=""
                              enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="card">
                                <div class="card-header">
                                    Stock Adjust Create
                                </div>
                                <hr>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-3 col-md-3 col-12">
                                            <div class="form-group">
                                                <label for="order_number"><b>Supplier<span class="text-danger">*</span></b></label>
                                                <select class="form-control bSelect" name="supplier_id" id="supplier_id"
                                                        required>
                                                    <option value="">Select One</option>
                                                    @foreach ($suppliers as $row)
                                                        <option value="{{ $row->id }}"
                                                            {{ old('supplier_id',$purchase->supplier_id) == $row->id ? 'selected' : '' }}>
                                                            {{ $row->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('supplier_id'))
                                                    <small
                                                        class="text-danger">{{ $errors->first('supplier_id') }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-md-3 col-12">
                                            <div class="form-group">
                                                <label for="ref_no"><b>Reference No</b></label>
                                                <input type="text" class="form-control" id="reference_no"
                                                       value="{{$purchase->reference_no}}" name="reference_no"
                                                       placeholder="Enter Reference No">
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-md-3 col-12">
                                            <div class="form-group">
                                                <label for="date"><b>Purchase Date<span class="text-danger">*</span></b></label>
                                                <input type="text" name="date"
                                                       class="form-control flatpickr-basic flatpickr-input active"
                                                       id="date" value="{{ old('date',date('Y-m-d')) }}">
                                                @if ($errors->has('date'))
                                                    <small class="text-danger">{{ $errors->first('date') }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-md-3 col-12">
                                            <div class="form-group">
                                                <label for="status"><b>Purchase Status</b></label>
                                                <select class="form-control bSelect" name="status" id="">
                                                    <option disabled>Select One</option>
                                                    <option value="received">Received</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-12 col-md-12 col-12">
                                            <div class="form-group">
                                                <label for="remark"><b>Remark</b></label>
                                                <textarea id="remark" name="remark" class="form-control input-sm"
                                                          rows="2">{{$purchase->remark}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="" id="vue_app">
                                <div class="card mb-1">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="product_id">Select Product </label>
                                                <select class="form-control bSelect" name="" id="product_id"
                                                        v-model="product_id" @change="fetch_product()">
                                                    <option value="">Select One</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}">
                                                            {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <span v-if="categoryLoading" class="categoryLoader">
                            <img src="{{ asset('loading.gif') }}" alt="loading">
                        </span>
                                <div class="card" v-if="products.length > 0">
                                    <div class="card-header">
                                        <h4 class="card-title">Select Product(@{{ products.length }})</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr style="background-color: #8080803b;">
                                                    <th>#</th>
                                                    <th colspan="2">Product Description</th>
                                                    <th>Unit</th>
                                                    <th>Unit Price</th>
                                                    <th>Quantity</th>
                                                    <th>Item Total</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(row, index) in products">
                                                    <td>
                                                        <b>@{{ index + 1 }}</b>
                                                    </td>
                                                    <td colspan="2">
                                                        <input type="hidden" :name="'products[' + index +
                                                                            '][product_id]'"
                                                               class="form-control input-sm"
                                                               v-bind:value="row.product_id">
                                                        <b>@{{ row.product_name }} -@{{ row.product_category }}</b>
                                                    </td>
                                                    <td>
                                                        <b>@{{ row.product_unit }} </b>
                                                    </td>
                                                    <td>
                                                        {{--                                                    <b>@{{ row.unit_price }} </b>--}}
                                                        <input type="number" :name="'products[' + index +
                                                                            '][unit_price]'"
                                                               class="form-control form-control-sm"
                                                               v-model="row.unit_price">
                                                    </td>
                                                    <td>
                                                        <input type="number" v-model="row.quantity" :name="'products[' + index +
                                                                            '][quantity]'"
                                                               class="form-control form-control-sm">
                                                    </td>
                                                    <td>
                                                        <b>@{{ row.unit_price*row.quantity }} </b>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                                @click="delete_row(row)"><i class="fa fa-times"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="10" style="background-color: #dddddd78;">
                                                    </td>
                                                </tr>
                                                <tfoot>
                                                <tr>
                                                    <td colspan="5" rowspan="5">
                                                    </td>
                                                    <td>
                                                        SubTotal
                                                    </td>
                                                    <td>
                                                        <input type="text" name="subtotal" class="form-control input-sm"
                                                               v-bind:value="subtotal" readonly>
                                                    </td>
                                                    <td rowspan="5">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Discount
                                                    </td>
                                                    <td>
                                                        <input type="text" name="discount" class="form-control input-sm"
                                                               v-model="discount">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Total
                                                    </td>
                                                    <td>
                                                        <input type="text" name="grand_total"
                                                               class="form-control input-sm" v-bind:value="grand_total"
                                                               readonly>
                                                    </td>
                                                </tr>
                                                <tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button class="btn btn-primary float-right" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
            <!-- Basic Inputs end -->
        </div>
    </div>

@endsection
@section('css')

@endsection
@section('js')

@endsection
@push('script')
    <script src="{{ asset('admin/app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('admin/app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('admin/app-assets/vendors/js/pickers/pickadate/picker.time.js') }}"></script>
    <script src="{{ asset('admin/app-assets/vendors/js/pickers/pickadate/legacy.js') }}"></script>
    <script src="{{ asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('admin/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
    <script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
    <script src="{{ asset('vue-js/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            var app = new Vue({
                el: '#vue_app',
                data: {
                    config: {
                        get_product_by_id_url: "{{ url('fetch-product-details-by-product-id') }}",
                        get_old_items_data: "{{ url('fetch-purchase-products-info') }}",
                    },
                    purchase_id: '{{ $purchase->id }}',
                    product_name: '',
                    product_id: '',
                    products: [],
                    discount: 0,
                    receive_amount: 0,
                    categoryLoading: false
                },
                computed: {

                    subtotal: function () {
                        return this.products.reduce((total, item) => {
                            return total + ((item.quantity * item.unit_price) - item.product_discount)
                        }, 0)
                    },
                    grand_total: function () {
                        return this.subtotal - this.discount
                    },
                },
                methods: {
                    fetch_product() {
                        var vm = this;
                        var slug = vm.product_id;
                        var exists = vm.products.some(function (field) {
                            return field.product_id == slug
                        });
                        if (exists) {
                            toastr.info('Product Already Selected', {
                                closeButton: true,
                                progressBar: true,
                            });
                        } else {
                            if (slug) {
                                // vm.results = [];
                                vm.categoryLoading = true;
                                console.log(vm.categoryLoading);
                                axios.get(this.config.get_product_by_id_url + '/' + slug).then(function (
                                    response) {
                                    var item_details = response.data;
                                    console.log(response.data);
                                    vm.products.push({
                                        product_id: item_details.id,
                                        product_name: item_details.name,
                                        product_sku: item_details.sku,
                                        product_category: item_details.category.name,
                                        product_unit: item_details.unit.name,
                                        unit_price: item_details.price,
                                        product_discount: 0,
                                        quantity: 0,
                                    });
                                    vm.categoryLoading = false;
                                    vm.product_id = '';
                                    console.log(vm.products);

                                }).catch(function (error) {
                                    toastr.error(error, {
                                        closeButton: true,
                                        progressBar: true,
                                    });
                                    return false;
                                });
                            }
                        }
                    },

                    load_old() {
                        var vm = this;
                        var slug = vm.purchase_id;
                        //  alert(slug);
                        axios.get(this.config.get_old_items_data + '/' + slug).then(function (response) {
                            var item = response.data;
                            for (key in item) {
                                vm.products.push(item[key]);
                            }
                        })
                    },

                    delete_row: function (row) {
                        this.products.splice(this.products.indexOf(row), 1);
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
