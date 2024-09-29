@extends('layouts.app')
@section('title')
    Sale Entry
@endsection
@section('styles')
    <style>
        input[name='date'] {
            height: calc(1.8125rem + 2px);
            padding: .25rem .5rem;
            font-size: .875rem;
            line-height: 1.5;
            border-radius: .2rem;
        }

        input[name='delivery_date'] {
            height: calc(1.8125rem + 2px);
            padding: .25rem .5rem;
            font-size: .875rem;
            line-height: 1.5;
            border-radius: .2rem;
        }

        .selected-discount-type {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
@endsection
@section('content')

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row mt-2" id="vue_app">
                <div class="col-lg-12 col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Sale Entry</h3>
                            <div class="card-tools">
                                <a href="{{route('sales.index')}}">
                                    <button class="btn btn-sm btn-primary">
                                        <i class="fa fa-list" aria-hidden="true"></i> &nbsp;Sale List
                                    </button>
                                </a>
                            </div>
                        </div>
                        <form action="{{ route('sales.store') }}" method="POST" class="" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="card-box">
                                    <div id="">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label for="date" class="small">Date</label>
                                                    <vuejs-datepicker v-model="date" name="date"
                                                                      placeholder="Select date"
                                                                      format="yyyy-MM-dd"
                                                                      @closed="getStoreData()"></vuejs-datepicker>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label for="" class="small">Current Store</label>
                                                    <select name="store_id" id="" class="form-control form-control-sm"
                                                            @if($user_store) disabled @endif @change="getStoreData()"
                                                            v-model="store_id">
                                                        <option value="">None</option>
                                                        @foreach($stores as $store)
                                                            <option
                                                                value="{{$store->id}}" {{ $user_store ?($user_store->id == $store->id ? 'selected' : '') :'' }}>{{ $store->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label for="" class="small">Delivery Point</label>
                                                    <select name="delivery_point_id" id=""
                                                            class="form-control form-control-sm"
                                                            v-model="delivery_point_id">
                                                        <option value="">None</option>
                                                        @foreach($delivery_points as $delivery_point)
                                                            <option
                                                                value="{{$delivery_point->id}}">{{ $delivery_point->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label for="date" class="small">Delivery Date</label>
                                                    <vuejs-datepicker v-model="delivery_date" name="delivery_date"
                                                                      placeholder="Select date"
                                                                      format="yyyy-MM-dd"></vuejs-datepicker>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label for="" class="small">Delivery Time</label>
                                                    <input type="time" v-model="delivery_time" name="delivery_time" id=""
                                                           class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            {{-- <div class="col-2">
                                                <div class="form-group">
                                                    <label for="text" class="small">Delivery Charge</label>
                                                    <input type="text" v-model="delivery_charge" name="delivery_charge" class="form-control form-control-sm" placeholder="Enter Delivery Charge">
                                                </div>
                                            </div> --}}
                                        </div>
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label for="" class="small">Sale Type</label>
                                                    <select name="sales_type" id="" class="form-control form-control-sm"
                                                            v-model="sales_type">
                                                        <option value="sales">SALES</option>
                                                        <option value="pre_order">PRE ORDER</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-3" v-if="sales_type=='pre_order'">
                                                <div class="form-group">
                                                    <label for="" class="small">Attachments</label>
                                                    <input type="file" name="attachments[]" id="" multiple
                                                           class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            <div class="col-6" v-if="sales_type=='pre_order'">
                                                <div class="form-group">
                                                    <label for="" class="small">Description</label>
                                                    <input type="text" name="description" id=""
                                                           class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            <div class="col-4" v-if="sales_type=='pre_order'">
                                                <div class="form-group">
                                                    <label for="" class="small">Size</label>
                                                    <input type="text" name="size" id=""
                                                           class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            <div class="col-4" v-if="sales_type=='pre_order'">
                                                <div class="form-group">
                                                    <label for="" class="small">Flavour</label>
                                                    <input type="text" name="flavour" id=""
                                                           class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            <div class="col-4" v-if="sales_type=='pre_order'">
                                                <div class="form-group">
                                                    <label for="" class="small">Cake Message</label>
                                                    <input type="text" name="cake_message" id=""
                                                           class="form-control form-control-sm">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="customer_id" class="small">Invoice Number</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                           placeholder="Enter Invoice Number" v-model="invoice_number">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="customer_id" class="small">Customer Number</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                           placeholder="Enter Customer Number and Press Enter"
                                                           v-model="customerNumber"
                                                           @keyup="getCustomerInfo" name="customer_number">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="store_id" class="small">Customer Name</label>
                                                    <input type="text" v-model="customer.name"
                                                           class="form-control form-control-sm"
                                                           disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label for="category_id" class="control-label small">Group</label>
                                                    <select class="form-control bSelect  form-control-sm"
                                                            name="category_id"
                                                            v-model="category_id" @change="fetch_item">
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
                                                    <label for="item_id" class="small">Item</label>
                                                    <select name="item_id" id="item_id"
                                                            class="form-control bSelect form-control-sm"
                                                            v-model="item_id">
                                                        <option value="">Select one</option>

                                                        <option :value="row.id" v-for="row in products"
                                                                v-html="row.name">
                                                        </option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="margin-top: 26px;">
                                                <button type="button" class="btn btn-sm btn-info btn-block"
                                                        @click="data_input">Add
                                                </button>
                                            </div>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" v-if="items.length > 0">
                                                <hr>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered small table-sm">
                                                        <thead class="thead-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Group</th>
                                                            <th>Item</th>
                                                            <th>Unit</th>
                                                            <th>Balance Qty</th>
                                                            <th>Selling Price</th>
                                                            <th>Quantity</th>
                                                            <th>Discount (TK)</th>
                                                            <th>Item total</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(row, index) in items">
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                        @click="delete_row(row)"><i
                                                                        class="fa fa-trash"></i></button>
                                                            </td>
                                                            <td>
                                                                @{{ row.group }}
                                                            </td>
                                                            <td>
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][item_id]'"
                                                                       class="form-control input-sm form-control-sm"
                                                                       v-bind:value="row.item_id">
                                                                <input type="hidden"
                                                                       :name="'products['+index+'][is_readonly]'"
                                                                       class="form-control input-sm form-control-sm"
                                                                       v-bind:value="row.is_readonly">
                                                                <input type="text"
                                                                       class="form-control input-sm  form-control-sm"
                                                                       :name="'products['+index+'][item_name]'"
                                                                       v-model="row.product_name"
                                                                       v-bind:readonly="row.is_readonly">
                                                            </td>
                                                            <td>
                                                                @{{ row.unit }}
                                                            </td>
                                                            <td>

                                                                <input type="text" :name="'products['+index+'][stock]'"
                                                                       class="form-control input-sm form-control-sm"
                                                                       v-bind:value="row.stock" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="number" v-model="row.price"
                                                                       :name="'products['+index+'][rate]'"
                                                                       class="form-control input-sm form-control-sm"
                                                                       @change="itemtotal(row)"
                                                                       v-bind:readonly="row.is_readonly">
                                                            </td>
                                                            <td>
                                                                <input type="number" v-model="row.quantity"
                                                                       :name="'products['+index+'][quantity]'"
                                                                       class="form-control input-sm form-control-sm"
                                                                       @change="valid(row)" required>
                                                            </td>
                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-4">
                                                                        <select
                                                                                class="form-control form-control-sm"
                                                                                v-model="row.discountType"
                                                                                :name="'products['+index+'][discountType]'"
                                                                                @change="updateProductDiscount(row)"
                                                                                :disabled="!row.discountable">
                                                                            <option value="f">tk</option>
                                                                            <option value="p">%</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-8">
                                                                        <input type="number"
                                                                               v-model="row.product_discount"
                                                                               :name="'products['+index+'][product_discount]'"
                                                                               class="form-control input-sm form-control-sm"
{{--                                                                               v-model="row.discountValue"--}}
                                                                               @keyup="updateProductDiscount(row)"
                                                                               :disabled="!row.discountable"
                                                                               required>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                       class="form-control input-sm form-control-sm"
                                                                       v-bind:value="itemtotal(row)" readonly>
                                                            </td>

                                                        </tr>

                                                        </tbody>
                                                        <tfoot>

                                                        <tr>
                                                            <td colspan="7" class="text-center">
                                                                <button class="btn btn-sm btn-outline-info"
                                                                        data-toggle="modal" data-target="#couponModal"
                                                                        type="button">Coupon
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-primary"
                                                                        data-toggle="modal" data-target="#discountModal"
                                                                        type="button">% Discount
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-dark"
                                                                        :class="selectedSpecialDiscount ? 'text-danger' : ''"
                                                                        :disabled="selectedNotDiscountableProduct"
                                                                        :style="selectedNotDiscountableProduct ? {cursor: 'not-allowed'} : ''"
                                                                        type="button" @click="addSpecialDiscount">
                                                                    Special
                                                                    Discount
                                                                    (2%)
                                                                </button>
                                                                <button class="btn btn-sm btn-warning" type="button"  data-toggle="modal" data-target="#exchangeModal">EXCHANGE</button>
                                                            </td>
                                                            <td>
                                                                SubTotal
                                                            </td>
                                                            <td>
                                                                <input type="text" name="subtotal"
                                                                       class="form-control input-sm form-control-sm"
                                                                       v-bind:value="total_bill" readonly>

                                                            </td>
                                                        </tr>
                                                        <tfoot>
                                                    </table>
                                                    <table class="table table-bordered small table-sm">
                                                        <thead class="thead-light">
                                                        <tr>
                                                            <th>Product Discounts</th>
                                                            <th>Coupon Discount</th>
                                                            <th>Overall Discount</th>
                                                            <th>Special Discount</th>
                                                            <th>Membership Discount <span>( @{{ membership_discount_percentage }} % )</span><br>
                                                                <span v-if="membership_discount_percentage > 0">Minimum Purchase <span>( @{{ minimum_purchase_amount }} TK )</span></span></th>
                                                            <th>Total Discount</th>
                                                            <th>Grand Total</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>@{{ productWiseDiscount }}</td>
                                                            <td>@{{ couponCodeDiscountAmount }}</td>
                                                            <td>@{{ total_discount_amount }}</td>
                                                            <td>@{{ special_discount_amount }}</td>
                                                            <td>@{{ membership_discount_amount }}</td>
                                                            <td>@{{ allDiscountAmount }}</td>
                                                            <td>@{{ total_payable_bill }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <hr>
                                                <div class="table-responsive">
                                                    <div class="row">
                                                        <div class="col-7">
                                                            <table class="table table-bordered small table-sm">
                                                                <thead class="thead-light">
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Method</th>
                                                                    <th>Amount</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr v-for="(payment, index) in paymentMethods">
                                                                    <td>
                                                                        <button type="button"
                                                                                class="btn btn-sm btn-danger"
                                                                                @click="deletePaymentMethod(payment)"><i
                                                                                class="fa fa-trash"></i></button>
                                                                    </td>
                                                                    <td>
                                                                        <select v-model="payment.method"
                                                                                :name="'payment_methods['+index+'][method]'"
                                                                                class="form-control form-control-sm">
                                                                            <option value="cash">Cash</option>
                                                                            <option value="bkash">Bkash</option>
                                                                            <option value="nagad">Nagad</option>
                                                                            <option value="DBBL">DBBL</option>
                                                                            <option value="UCB">UCB</option>
                                                                            <option value="rocket">Rocket</option>
                                                                            <option value="upay">Upay</option>
                                                                            <option value="point">Redeem Point</option>
                                                                            <option value="exchange">Exchange</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" v-model="payment.amount"
                                                                               :step="payment.method == 'point' ? 100 : 1"
                                                                               :name="'payment_methods['+index+'][amount]'"
                                                                               @key.press="checkPointInput"
                                                                               class="form-control form-control-sm" v-bind:readonly="payment.method == 'exchange' && exchangeAmount > 0">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="3" class="text-right">
                                                                        <button class="btn btn-sm btn-secondary"
                                                                                type="button"
                                                                                @click="addMorePaymentMethod"> Add
                                                                            Another Payment Method
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-5">
                                                            <table class="table">
                                                                <tr>
                                                                    <th>Payable Amount</th>
                                                                    <td>
                                                                        <input type="text" class="form-control input-sm form-control-sm"
                                                                               v-model="pay_left" disabled>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Receive Amount</th>
                                                                    <td>
                                                                        <input type="text" name="receive_amount"
                                                                               class="form-control input-sm form-control-sm"
                                                                               v-model="total_paying" disabled>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <th>Change Amount</th>
                                                                    <td>
                                                                        <input type="text" name="change_amount"
                                                                               class="form-control input-sm form-control-sm"
                                                                               v-model="cash_change" disabled>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            {{--                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"--}}
                                            {{--                                                 v-if="items.length > 0">--}}
                                            {{--                                                <div class="text-right col-lg-12 col-md-12 col-sm-12 col-xs-12">--}}
                                            {{--                                                        <textarea class="form-control form-control-sm" name="comments" rows="5"--}}
                                            {{--                                                                  placeholder="Enter Comments"></textarea>--}}
                                            {{--                                                </div>--}}
                                            {{--                                            </div>--}}
                                        </div>


                                    </div>
                                </div>
                                {{--New Discounts--}}
                                <input type="hidden" name="membership_discount_percentage"
                                       v-model="membership_discount_percentage">
                                <input type="hidden" name="membership_discount_amount"
                                       v-model="membership_discount_amount">
                                <input type="hidden" name="special_discount_value" v-model="special_discount_value">
                                <input type="hidden" name="special_discount_amount" v-model="special_discount_amount">
                                <input type="hidden" name="couponCode" v-model="couponCode">
                                <input type="hidden" name="couponCodeDiscountType" v-model="couponCodeDiscountType">
                                <input type="hidden" name="couponCodeDiscountValue" v-model="couponCodeDiscountValue">
                                <input type="hidden" name="couponCodeDiscountAmount" v-model="couponCodeDiscountAmount">
                                <input type="hidden" name="total_discount_type" v-model="total_discount_type">
                                <input type="hidden" name="total_discount_value" v-model="total_discount_value">
                                <input type="hidden" name="total_discount_amount" v-model="total_discount_amount">
                                <input type="hidden" name="discount" v-model="allDiscountAmount">
                                <input type="hidden" name="grandtotal" v-model="total_payable_bill">
                                <input type="hidden" name="returnNumber" v-model="returnNumber">
                                <input type="hidden" name="exchangeAmount" v-model="exchangeAmount">
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right"
                                 v-if="items.length > 0 && ((!customerNumber && total_payable_bill <= total_paying) || customerNumber)">
                                <button class="float-right btn btn-primary" type="submit"><i
                                        class="fa fa-fw fa-lg fa-save"></i>Save
                                </button>
                            </div>
                        </form>
                    </div>
                    <!--Modals-->
                    <div class="modal fade" id="couponModal" tabindex="-1" role="dialog"
                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Apply Coupon</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="text" placeholder="Enter Coupon Code" v-model="couponCode"
                                           class="form-control">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" :disabled="couponCode.length < 1"
                                            v-on:click="getCouponDiscountValue">Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="discountModal" tabindex="-1" role="dialog"
                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Apply Discount</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-2">
                                        <h4>Select Discount Type</h4>

                                        <div class="text-center">
                                            <button class="btn btn-sm btn-outline-primary"
                                                    :class="total_discount_type == 'fixed' ? 'selected-discount-type' : '' "
                                                    @click="setDiscountType('fixed')">Fixed
                                            </button>
                                            <button class="btn btn-sm btn-outline-info"
                                                    :class="total_discount_type == 'percentage' ? 'selected-discount-type' : '' "
                                                    @click="setDiscountType('percentage')">Percentage
                                            </button>

                                        </div>
                                    </div>
                                    <input type="number" min="0" step="0.01" placeholder="Enter Discount Amount"
                                           v-model="total_discount_value"
                                           class="form-control">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" data-dismiss="modal" v-on:click="updateDiscount">Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="exchangeModal" tabindex="-1" role="dialog"
                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Apply Exchange</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="text" placeholder="Enter Sales Return Number" v-model="returnNumber"
                                           class="form-control">
                                    <a href="{{ route('sales-returns.create') }}" target="_blank">Create New Return</a>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" data-dismiss="modal" :disabled="returnNumber.length < 1"
                                            v-on:click="getReturnNumberValue">Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div>
            <!-- /.row -->

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
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
                        get_items_info_by_group_id_url: "{{ url('fetch-items-by-group-id') }}",
                        get_product_info_url: "{{ url('fetch-item-by-id-for-sale') }}",
                        get_customer_url: "{{ url('pos-customer-by-number') }}",
                        get_coupon_code_value_url: "{{ url('pos-coupon-code-discount') }}",
                        get_return_value_url: "{{ url('pos--return-number-amount') }}",
                    },
                    customer_id: '',
                    store_id: "",
                    category_id: '',
                    item_id: '',
                    products: [],
                    items: [],
                    quantity: '',
                    Stock_quantity: 0,
                    price: 0,
                    discount: 0,
                    product_discount: 0,
                    receive_amount: 0,
                    selling_price: 0,
                    customerNumber: '',
                    customer: {},
                    date: new Date(),
                    invoice_number: "{{ $invoice_number ?? 'Please Select Store First' }}",
                    paymentMethods: [{
                        amount: 0,
                        method: 'cash'
                    }],
                    delivery_point_id: '',
                    delivery_date: new Date(),
                    delivery_time: '',
                    delivery_charge: '',
                    sales_type: 'sales',
                    membership_discount_percentage: 0,
                    minimum_purchase_amount: 0,
                    couponCode: '',
                    couponCodeDiscountType: '',
                    couponCodeDiscountValue: 0,
                    couponCodeDiscountShowValue: '',
                    couponCodeDiscountAmount: 0,
                    total_discount_type: '',
                    total_discount_value: 0,
                    total_discount_amount: 0,
                    special_discount_value: 2,
                    special_discount_amount: 0,
                    selectedSpecialDiscount: false,
                    returnNumber: '',
                    exchangeAmount: 0,
                    user_outlet_id: "",
                },
                components: {
                    vuejsDatepicker
                },
                mounted: function () {
                    this.setStoreId()
                },
                computed: {
                    subtotal: function () {
                        return this.items.reduce((total, item) => {
                            return total + ((item.quantity * item.price) - item.product_discount)
                        }, 0)
                    },
                    grandtotal: function () {
                        return this.subtotal - this.discount
                    },
                    change_amount: function () {
                        return this.grandtotal - this.receive_amount
                    },
                    total_bill: function () {
                        return this.items.reduce((total, item) => {
                            return total + ((item.quantity * item.price))
                        }, 0)
                    },
                    total_paying: function () {
                        let amount = this.paymentMethods.reduce((total, item) => {
                            return Number(total) + Number(item.method ? item.amount : 0)
                        }, 0)
                        if (amount > 0) {
                            if (this.items.length < 1) {
                                toastr.error('Please Add Items', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                                return 0;
                            }
                        }
                        return amount
                    },
                    pay_left: function () {
                        return this.total_paying > this.total_due ? 0 : this.total_due - this.total_paying
                    },
                    cash_change: function () {
                        return this.total_paying > this.total_due ? this.total_paying - this.total_due : 0
                    },
                    total_payable_bill: function () {
                        var vm = this
                        return (this.total_bill - vm.couponCodeDiscountAmount - this.allDiscountAmount)
                    },
                    total_due: function () {
                        return this.total_payable_bill
                    },
                    productWiseDiscount: function () {
                        return this.items.reduce((total, item) => {
                            return total + Number(item.discountAmount)
                        }, 0)
                    },
                    allDiscountAmount: function () {
                        var vm = this
                        return Number(vm.total_discount_amount) + Number(vm.special_discount_amount) + Number(this.productWiseDiscount) + Number(this.membership_discount_amount)
                    },
                    membership_discount_amount: function () {
                        var vm = this
                        return vm.total_bill > vm.minimum_purchase_amount ? (Number(vm.total_bill) * Number(vm.membership_discount_percentage) / 100) : 0
                    },
                    selectedNotDiscountableProduct: function () {
                        return this.items.some(item => !item.discountable);
                    }
                },
                methods: {
                    fetch_item() {
                        var vm = this;
                        var slug = vm.category_id;
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
                        var vm = this;
                        if (!vm.store_id) {
                            toastr.error('Please Select Store', {
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
                            let slug = vm.item_id;
                            let exists = vm.items.some(function (field) {
                                return field.item_id == slug
                            });

                            if (exists) {
                                toastr.info('Item Already Selected', {
                                    closeButton: true,
                                    progressBar: true,
                                });
                            } else {
                                if (slug) {
                                    axios.get(this.config.get_product_info_url + '/' + slug, {
                                        params: {
                                            store_id: vm.store_id
                                        }
                                    }).then(function (response) {
                                        let product_details = response.data;
                                        vm.items.push({
                                            item_id: vm.item_id,
                                            group: product_details.group,
                                            product_name: product_details.name,
                                            unit: product_details.unit,
                                            stock: product_details.balance_qty,
                                            price: product_details.price,
                                            sale_price: product_details.price,
                                            quantity: '',
                                            product_discount: 0,
                                            subtotal: 0,
                                            is_readonly: product_details.is_readonly,
                                            discountable: product_details.parent_id !== 73,
                                            discountType: '',
                                            discountValue: 0,
                                            discountAmount: 0,
                                        });
                                        // vm.item_id = '';
                                        // vm.category_id = '';

                                    }).catch(function (error) {
                                        toastr.error('Something went to wrong', {
                                            closeButton: true,
                                            progressBar: true,
                                        });
                                        return false;
                                    });
                                }
                            }
                        }
                    },
                    delete_row: function (row) {
                        this.items.splice(this.items.indexOf(row), 1);
                    },
                    itemtotal: function (index) {

                        return (index.quantity * index.price) - index.discountAmount;

                    },
                    valid: function (index) {
                        const vm=this
                        if (index.quantity > index.stock && index.is_readonly && (!vm.delivery_point_id || vm.user_outlet_id == vm.delivery_point_id) && vm.sales_type == 'sales') {
                            index.quantity = index.stock;
                        }
                        if (index.quantity <= 0) {
                            index.quantity = '';
                        }
                    },
                    getCustomerInfo() {
                        var vm = this;
                        vm.customer = {}
                        axios.get(this.config.get_customer_url, {
                            params: {
                                number: this.customerNumber
                            }
                        }).then(function (response) {
                            vm.customer = (response.data);
                            if (vm.customer.name) {
                                vm.customer.name = vm.customer.name + '  (' + vm.customer.reedemible_point + '  point)'
                                vm.membership_discount_percentage = vm.customer.purchase_discount
                                vm.minimum_purchase_amount = vm.customer.minimum_purchase
                            }
                        }).catch(function (error) {
                            toastr.error(error, {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });
                    },
                    getStoreData() {
                        const vm = this
                        if (!vm.store_id) {
                            vm.invoice_number = 'Please Select Store First'
                            toastr.error('Please Select valid Store', {
                                closeButton: true,
                                progressBar: true,
                            });
                        }
                        axios.get('/invoice-by-store/' + vm.store_id, {
                            params: {
                                date: vm.date
                            }
                        }).then((response) => {
                            vm.invoice_number = response.data.invoice
                            vm.user_outlet_id = response.data.outlet
                        })
                    },
                    deletePaymentMethod: function (row) {
                        this.paymentMethods.splice(this.paymentMethods.indexOf(row), 1);
                    },
                    addMorePaymentMethod() {
                        this.paymentMethods.push({amount: 0, method: ''})
                    },
                    setStoreId() {
                        const vm = this
                        vm.store_id = "{{ $user_store ?  $user_store->id : ''}}"
                        vm.user_outlet_id = "{{ $user_store ?  $user_outlet_id : ''}}"
                    },
                    getCouponDiscountValue() {
                        var vm = this;
                        axios.get(this.config.get_coupon_code_value_url + '?code=' + vm.couponCode + '&user=' + vm.customerNumber)
                            .then(function (response) {
                                const responseData = response.data
                                if (responseData.success === false) {
                                    toastr.error(responseData.message, {
                                        closeButton: true,
                                        progressBar: true,
                                    });
                                } else {
                                    if (responseData.data.minimum_purchase && responseData.data.minimum_purchase > vm.total_payable_bill) {
                                        toastr.error('Minimum Purchase Amount is ' + responseData.data.minimum_purchase, {
                                            closeButton: true,
                                            progressBar: true,
                                        });
                                    }
                                    vm.couponCodeDiscountType = responseData.data.discount_type
                                    vm.couponCodeDiscountValue = responseData.data.discount_value
                                    vm.minimumPurchaseAmount = responseData.data.minimum_purchase
                                    if (vm.couponCodeDiscountType === 'fixed') {
                                        vm.couponCodeDiscountAmount = vm.couponCodeDiscountValue
                                        vm.couponCodeDiscountShowValue = vm.couponCodeDiscountValue + ' TK'
                                    }
                                    if (vm.couponCodeDiscountType === 'percentage') {
                                        vm.couponCodeDiscountAmount = (vm.total_bill * vm.couponCodeDiscountValue) / 100
                                        vm.couponCodeDiscountShowValue = vm.couponCodeDiscountValue + ' %'
                                        vm.couponCodeDiscountAmount = Math.round(vm.couponCodeDiscountAmount)
                                    }
                                    vm.couponModalShow === true ? vm.couponModalShow = false : true
                                }

                            }).catch(function (error) {
                            toastr.error(error, {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });
                    },
                    updateDiscount() {
                        var vm = this
                        if (vm.productWiseDiscount > 0) {
                            if (confirm("Single Product Discount Applied, Sure to add Total Discount ?")) {
                                if (this.total_discount_type === 'fixed') {
                                    vm.total_discount_amount = vm.total_discount_value
                                }
                                if (this.total_discount_type === 'percentage') {
                                    vm.total_discount_amount = (vm.total_bill * vm.total_discount_value) / 100
                                    vm.total_discount_amount = Math.round(vm.total_discount_amount)
                                }
                                vm.total_discount_value = 0;
                            }
                        }else{
                            if (this.total_discount_type === 'fixed') {
                                vm.total_discount_amount = vm.total_discount_value
                            }
                            if (this.total_discount_type === 'percentage') {
                                vm.total_discount_amount = (vm.total_bill * vm.total_discount_value) / 100
                                vm.total_discount_amount = Math.round(vm.total_discount_amount)
                            }
                            vm.total_discount_value = 0;
                        }

                    },
                    setDiscountType(discountType) {
                        this.total_discount_type = discountType
                    },
                    addSpecialDiscount() {
                        var vm = this
                        if (vm.productWiseDiscount > 0) {
                            if (confirm("Single Product Discount Applied, Sure to add Total Discount ?")) {
                                if (vm.selectedSpecialDiscount) {
                                    vm.selectedSpecialDiscount = false
                                    vm.special_discount_amount = 0
                                } else {
                                    vm.selectedSpecialDiscount = true
                                    vm.special_discount_amount = (vm.total_bill * vm.special_discount_value) / 100
                                }
                                vm.special_discount_amount = Math.round(vm.special_discount_amount)
                            } else {
                                return false;
                            }
                        } else {
                            if (vm.selectedSpecialDiscount) {
                                vm.selectedSpecialDiscount = false
                                vm.special_discount_amount = 0
                            } else {
                                vm.selectedSpecialDiscount = true
                                vm.special_discount_amount = (vm.total_bill * vm.special_discount_value) / 100
                            }
                            vm.special_discount_amount = Math.round(vm.special_discount_amount)
                        }

                    },
                    updateProductDiscount(sp) {
                        var vm = this
                        if (vm.total_discount_amount > 0) {
                            if (confirm("Total Discount Applied, Sure to add Total Discount Single Product Discount?")) {
                                this.items.some(function (product) {
                                    product.discountValue = product.product_discount
                                    var total_cost = (product.quantity * product.price);
                                    if (product.discountType == 'p') {
                                        product.discountAmount = (total_cost * product.discountValue) / 100;
                                    }
                                    if (product.discountType == 'f') {
                                        product.discountAmount = product.discountValue;
                                    }
                                    product.discountAmount = Math.round(product.discountAmount)
                                });
                            } else {
                                sp.discountType = ''
                                sp.discountValue = 0
                                return false;
                            }
                        } else {
                            this.items.some(function (product) {
                                product.discountValue = product.product_discount
                                var total_cost = (product.quantity * product.price);
                                if (product.discountType == 'p') {
                                    product.discountAmount = (total_cost * product.discountValue) / 100;
                                }
                                if (product.discountType == 'f') {
                                    product.discountAmount = product.discountValue;
                                }
                                product.discountAmount = Math.round(product.discountAmount)
                            });
                        }

                    },
                    getReturnNumberValue() {
                        var vm = this;
                        axios.get(this.config.get_return_value_url + '?returnNumber=' + vm.returnNumber)
                            .then(function (response) {
                                const responseData = response.data
                                if (responseData.success === false) {
                                    toastr.error(responseData.message, {
                                        closeButton: true,
                                        progressBar: true,
                                    });
                                } else {
                                    vm.exchangeAmount = responseData.amount
                                    vm.paymentMethods.push({
                                        amount: vm.exchangeAmount,
                                        method: 'exchange'
                                    })
                                }


                            }).catch(function (error) {
                            toastr.error(error, {
                                closeButton: true,
                                progressBar: true,
                            });
                            vm.returnNumber = "";
                            return false;
                        });
                    },
                },
                updated() {
                    $('.bSelect').selectpicker('refresh');
                },
            });
            $('.bSelect').selectpicker({
                liveSearch: true,
                size: 5
            });
        });
    </script>
@endpush
