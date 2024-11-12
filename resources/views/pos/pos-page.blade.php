<template>
    <div id="pos-page">
        <div class="row">
            <div class="col-6">
                <div class="row">
                    <div class="col-4 text-center header-gap">
                        <h4>All Items</h4>
                        <ul style="list-style-type: none; height: 80vh; overflow-y: auto; cursor: pointer">
                            <li style="border: 1px solid #dedede; border-radius: 5px; margin-bottom: 10px"
                                v-for="(category, index) in categories" v-bind:value="category.id"
                                @click="clickedOnCategory(category.id)">
                                <div style="position: relative; text-align: center; color: white">
                                    <img
                                        src="https://as2.ftcdn.net/v2/jpg/03/33/60/19/1000_F_333601933_hSdfWhDfRG3zaiVRvYZF24KixdVBdGfB.jpg"
                                        alt="" width="100%" height="70px">
                                    <p style="position:absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: 700; font-size: small; color: black">
                                        @{{ category.name }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-8 header-gap" style="background-color: #dedede">
                        <div class="m-2 mt-3 d-flex">
                            <h4 style="margin-right: 10px">Products</h4>
                            <input type="text" class="form-control" placeholder="Search Product by Title, SKU"
                                   v-model="search_string" @keyup="getProductBySearchString()">
                        </div>
                        <div>
                            <table class="table table-scroll" width="100%">
                                <thead class="new-table-header">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Price/Unit</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="new-table-row" v-for="(row, index) in products"
                                    v-on:click="selectProductToSell(row)">
                                    <td>@{{ row.name }}</td>
                                    <td>TK. @{{ row.price }}</td>
                                    <td :class="row.stock > 0 ? 'inStock' :'outStock'">Stock (@{{ row.stock }})</td>
                                </tr>
                                <tr class="blank-row"></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="m-2">
                    <div class="row">
                        <div class="col-6">
                            <input type="text" class="form-control phone-input" placeholder="Enter Phone Number"
                                   v-model="customerNumber"
                                   @keydown.enter="getCustomerInfo">
                        </div>
                        <div class="col-6" style="line-height: 10px">
                            <p>Customer Name: @{{ customer ? customer.name : 'Not Found' }}</p>
                            <p>Customer Point: @{{ customer ? customer.reedemible_point : 'Not Found' }} <span style="color: cornflowerblue">(@{{ customer ? customer.member_type_name : 'Not Found' }})</span></p>
                            <a v-if="customer && customer.reedemible_point > 100" @click="getPointRedeemField">[Redeem]</a>
                        </div>
                    </div>
                </div>
                <div style="min-height: 45vh">
                    <h4>Cart Items</h4>
                    <div class="row" style="max-height: 45vh; overflow-y: auto">
                        <div class="col-12 mb-2" v-for="(product,index) in selectedProducts">
                            <div class="product-info p-2">
                                <div>
                                    <span>  @{{ product.name }}  <small>[TK.@{{ product.price }} x @{{ product.quantity }}]</small></span>
                                    <span class="float-right">TK.@{{ product.total }} <small>(ex.tax)</small></span>
                                </div>
                                <div class="row ml-5 mr-5" style="font-size: small">
                                    <div class="col-6 input-group " style="gap: 5px">
                                        <label>Quantity</label>
                                        <input type="number" class="form-control" style="height: 25px;font-size: x-small"  min="1" :max="product.stock" v-model="product.quantity" @keyup="updateQuantity(product, 'false')">
                                    </div>
                                    <div class="col-6 input-group" style="gap: 5px">
                                        <input type="text" class="form-control"
                                               aria-label="Text input with dropdown button" style="height: 25px;font-size: x-small"  v-model="product.discountValue" @keyup="updateProductDiscount(product)" :disabled="!product.discountable">
                                        <div class="input-group-append">
                                            <select name="" id="" class="form-control"  style="height: 25px;font-size: x-small"  v-model="product.discountType" @change="updateProductDiscount(product)" :disabled="!product.discountable">
                                                <option value="">Discount</option>
                                                <option value="p">%</option>
                                                <option value="f">TK</option>
                                            </select>
{{--                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"--}}
{{--                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"--}}
{{--                                                    style="height: 25px; font-size: small">Discount--}}
{{--                                            </button>--}}
{{--                                            <div class="dropdown-menu">--}}
{{--                                                <a class="dropdown-item" href="#">%</a>--}}
{{--                                                <a class="dropdown-item" href="#">TK</a>--}}
{{--                                            </div>--}}
                                        </div>
                                        <button class="btn btn-sm btn-danger" @click="delete_selected_product(product)">X</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <ul style="list-style-type: none; font-size:12px">
                        <li><span>Subtotal</span><span style="float: right">TK.@{{ total_bill }}</span></li>
                        <li><span>Products Discount</span><span style="float: right">TK.@{{ productWiseDiscount }}</span></li>

                        <li><span>Coupon Discount</span><span style="float: right"><strong><span
                                        v-if="couponCodeDiscountShowValue">( @{{ couponCodeDiscountShowValue }} )</span>@{{ couponCodeDiscountAmount ?? 'N/A' }}</strong></span>
                        </li>
                        <li><span>Overall Discount</span><span style="float: right">TK.@{{ total_discount_amount }}</span></li>
                        <li><span>Special Discount</span><span style="float: right">TK.@{{ special_discount_amount }}</span></li>
                        <li><span>Membership Discount (@{{customer && customer.purchase_discount > 0 ? customer.purchase_discount +'% @ '+customer.minimum_purchase + 'TK' : ''}} )</span><span style="float: right">TK.@{{ membership_discount_amount }}</span></li>
                        <li><span>Total Discount</span><span style="float: right">TK.@{{ allDiscountAmount }}</span></li>
                    </ul>
                </div>
                <div class="container text-center btn-group btn-group-justified" style="gap: 10px">
                    <button class="btn discount-button smallFont" @click="openCouponModal">Coupon</button>
                    <button class="btn discount-button smallFont" @click="openDiscountModal" :disabled="selectedNotDiscountableProduct" :style="selectedNotDiscountableProduct ? {cursor: 'not-allowed'} : ''">% Discount</button>
                    <button class="btn discount-button smallFont" @click="addSpecialDiscount" :class="selectedSpecialDiscount ? 'text-danger' : ''"  :disabled="selectedNotDiscountableProduct" :style="selectedNotDiscountableProduct ? {cursor: 'not-allowed'} : ''">@{{ special_discount_value }}% Special Discount</button>
                    <button class="btn pause-button smallFont" @click="openOnHoldModal">On Hold</button>
                </div>
                <div class=" payment-button mt-2 p-3" style="cursor: pointer" @click="openPaymentModal">
                    <div>
                        <h5>Process To Pay</h5>
                        <span>@{{ total_items }} Items</span>
                        <span class="float-right">TK. @{{ total_payable_bill }} >></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <b-modal ref="coupon-modal" hide-footer title="Apply Coupon">
        <div class="d-block text-center">
            <input type="text" placeholder="Enter Coupon Code" v-model="couponCode" class="form-control">
        </div>
        <b-button class="mt-3" variant="outline-danger" @click="closeCouponModal">Close</b-button>
        <b-button class="mt-3" variant="outline-info" :disabled="couponCode.length < 1"
                  v-on:click="getCouponDiscountValue">Apply
        </b-button>
    </b-modal>

    <b-modal ref="discount-modal" hide-footer title="Apply Discount">
        <div class="d-block text-center">
            <div class="mb-2">
                <h4>Select Discount Type</h4>

                <div class="text-center">
                    <button class="btn discount-type-button"
                            :class="total_discount_type == 'fixed' ? 'selected-discount-type' : '' "
                            @click="setDiscountType('fixed')">Fixed
                    </button>
                    <button class="btn discount-type-button"
                            :class="total_discount_type == 'percentage' ? 'selected-discount-type' : '' "
                            @click="setDiscountType('percentage')">Percentage
                    </button>

                </div>
            </div>
            <input type="number" min="0" step="0.01" placeholder="Enter Discount Amount" v-model="total_discount_value"
                   class="form-control">
        </div>
        <b-button class="mt-3" variant="outline-danger" @click="closeDiscountModal">Close</b-button>
        <b-button class="mt-3" variant="outline-info" v-on:click="updateDiscount">
            Apply
        </b-button>
    </b-modal>

    <b-modal ref="payment-modal" hide-footer title="Payment">
        <div class="d-block text-center">
            <b-container class="bv-example-row">
                <b-row>
                    <b-col class="payCard mr-2">
                        <h5>Total Due</h5>
                        <span style="color: darkred">TK.@{{ total_due }}</span>
                    </b-col>
                    <b-col class="payCard mr-2">
                        <h5>Paying</h5>
                        <span style="color: forestgreen">TK.@{{ total_paying }}</span>
                    </b-col>
                    <b-col class="payCard mr-2">
                        <h5>Payable</h5>
                        <span style="color: darkorange">TK.@{{ pay_left }}</span>
                    </b-col>
                    <b-col class="payCard">
                        <h5>Change</h5>
                        <span style="color: darkslategray">TK.@{{ cash_change }}</span>
                    </b-col>
                </b-row>
            </b-container>
            <b-container class="mt-5 text-center">
                <b-row>
                    <b-col cols="5" class="mr-2">
                        <h5>Amount</h5>
                    </b-col>
                    <b-col cols="5" class="mr-2">
                        <h5>Method</h5>
                    </b-col>
                    <b-col  cols="1">
                    </b-col>
                </b-row>
                <b-row class="mt-2" v-for="(payment, index) in paymentMethods" :key="index">
                    <b-col cols="5" class="mr-2">
                        <input type="text" class="form-control"   v-model="payment.amount" :step="payment.method == 'point' ? 100 : 1" @key.press="checkPointInput">
                    </b-col>
                    <b-col  cols="5" class="mr-2">
                        <select class="form-control"  v-model="payment.method" @change="checkAvail(index)">
                            <option value="cash">Cash</option>
                            <option value="bkash">Bkash</option>
                            <option value="nagad">Nagad</option>
                            <option value="DBBL">DBBL</option>
                            <option value="UCB">UCB</option>
                            <option value="upay">Upay</option>
                            <option value="rocket">Rocket</option>
                            <option value="nexus">Nexus</option>
                            <option value="pbl">PBL POS</option>
                            <option value="due">Due Sale</option>
                            <option value="point">Redeem Point</option>
                        </select>
                    </b-col>
                    <b-col  cols="1"  v-if="index !==0" @click="deletePaymentMethod(payment)">Delete</b-col>
                </b-row>
                <button class="mt-2 btn btn-info"  @click="addMorePaymentMethod">Add Another Payment Method</button>
                <b-row class="mt-2">
                    <select name="" id="" class="form-control" v-model="waiter_id">
                        <option value="">Select Waiter</option>
                       @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->employee_id.'  -- '. $employee->name }}</option>
                       @endforeach
                    </select>
                </b-row>
            </b-container>
        </div>
        <div class="text-center">
            <b-button class="mt-3" variant="btn btn-secondary" @click="closePaymentModal">Cancel</b-button>
            <b-button class="mt-3" variant="btn btn-info"  :disabled="(pay_left > 0) || isDisabled" @click="submitOrder">  Pay </b-button>
        </div>
    </b-modal>

    <b-modal ref="on-hold-modal" hide-footer title="On Hold">
        <div class="d-block text-center">
            <input type="text" placeholder="Enter an Identifier" v-model="onHoldIdentifier" class="form-control">
        </div>
        <div class="text-center">
            <b-button class="mt-3" variant="outline-danger" @click="closeOnHoldModal">Close</b-button>
            <b-button class="mt-3" variant="outline-info" :disabled="selectedProducts.length < 1"
                      v-on:click="storeHoldOrder">Apply
            </b-button>
        </div>
    </b-modal>

    <b-modal ref="on-hold-order-modal" hide-footer title="On Hold Orders">
        <div class="d-block text-center">
           <div class="row m-2">
               <div class="col-12" v-for="(row, index) in holdOrders" style="border: 2px solid #dedede; border-radius: 5px; cursor: pointer" @click="addHoldOrderToPos(row)">
                   <span class="float-left">Identifier: @{{ row.identifier }}</span>
                   <span>Product: @{{ row.items.length }}</span>
                   <span class="float-right">Quantity: @{{ row.total }}</span>
               </div>
           </div>
        </div>
        <div class="text-center">
            <b-button class="mt-3" variant="outline-danger" @click="closeOnHoldOrderModal">Close</b-button>
{{--            <b-button class="mt-3" variant="outline-info"--}}
{{--                      v-on:click="addHoldOrderToPos">Add To POS--}}
{{--            </b-button>--}}
        </div>
    </b-modal>

    <b-modal ref="pre-order-modal" hide-footer title="Pre Order">
        <div class="d-block">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="">Delivery Date</label>
                        <input type="datetime-local" class="form-control" v-model="preOrderValues.delivery_date">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="">Order From</label>
                        <select name="order_from" id="" class="form-control" v-model="preOrderValues.order_from">
                            <option value="facebook" selected>Facebook</option>
                            <option value="outlet" selected>Outlet</option>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="">Advance Payment</label>
                        <input type="number" placeholder="ex: 500" class="form-control" v-model="preOrderValues.advance_payment">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="">Paid By</label>
                        <select name="paid_by" id="" class="form-control" v-model="preOrderValues.paid_by">
                            <option value="" selected>Select an Option</option>
                            <option value="cash" >Cash</option>
                            <option value="bkash" >Bkash</option>
                            <option value="nagad" >Nagad</option>
                            <option value="card" >Card</option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="">Comments</label>
                        <textarea name="remarks" id="" rows="2" class="form-control" v-model="preOrderValues.comment"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <b-button class="mt-3" variant="outline-danger" @click="closePreOrderModal">Close</b-button>
            <b-button class="mt-3" variant="outline-info" :disabled="selectedProducts.length < 1"
                      v-on:click="storePreOrder()">Submit Order
            </b-button>
        </div>
    </b-modal>

</template>
