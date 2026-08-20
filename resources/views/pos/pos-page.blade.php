<template>
    <div id="pos-page" class="pos-shell">
        <div class="row">
            <div class="col-6 pos-catalog">
                <div class="row">
                    <div class="col-4">
                        <h5 class="mb-2">Categories</h5>
                        <ul class="pos-cats">
                            <li>
                                <button type="button" class="pos-cat" :class="selected_category === '' ? 'active' : ''" @click="clickedOnCategory('')">All items</button>
                            </li>
                            <li v-for="(category, index) in categories" :key="category.id">
                                <button type="button" class="pos-cat" :class="selected_category == category.id ? 'active' : ''" @click="clickedOnCategory(category.id)">@{{ category.name }}</button>
                            </li>
                        </ul>
                    </div>
                    <div class="col-8">
                        <div class="pos-products">
                        <div class="m-0 mb-2 d-flex align-items-center">
                            <h5 class="mb-0 mr-2">Products</h5>
                            <input type="text" class="form-control" placeholder="Search product or scan"
                                   v-model="search_string" @keyup="getProductBySearchString()">
                        </div>
                        <div>
                            <table class="table table-scroll" width="100%">
                                <thead class="new-table-header">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="new-table-row" v-for="(row, index) in products"
                                    v-on:click="selectProductToSell(row)">
                                    <td>@{{ row.name }}</td>
                                    <td>TK. @{{ row.price }}</td>
                                    <td :class="row.stock > 0 ? 'inStock' :'outStock'">@{{ row.stock }}</td>
                                </tr>
                                <tr class="blank-row"></tr>
                                <tr v-if="productsLoading">
                                    <td colspan="3" class="text-center">Loading products...</td>
                                </tr>
                                <tr v-else-if="productsHasMore">
                                    <td colspan="3" class="text-center">
                                        <button type="button" class="btn btn-sm new-button" @click.stop="loadMoreProducts">Load More</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 pos-ticket">
                <div class="pos-customer">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <input type="text" class="form-control phone-input" placeholder="Customer phone"
                                   v-model="customerNumber"
                                   @keydown.enter="getCustomerInfo">
                        </div>
                        <div class="col-6">
                            <p class="mb-1"><strong>@{{ customer && customer.name ? customer.name : 'Walking customer' }}</strong></p>
                            <p class="mb-0 small">Points: @{{ customer ? customer.reedemible_point : 0 }}
                                <span v-if="customer && customer.member_type_name">(@{{ customer.member_type_name }})</span>
                            </p>
                            <a href="#" v-if="customer && customer.reedemible_point > 100" @click.prevent="getPointRedeemField">Redeem</a>
                        </div>
                    </div>
                </div>
                <div style="min-height: 38vh">
                    <h5>Cart</h5>
                    <div class="row" style="max-height: 38vh; overflow-y: auto">
                        <div class="col-12 mb-2" v-if="selectedProducts.length < 1">
                            <div class="product-info p-3 text-muted">No items yet. Tap a product to add it.</div>
                        </div>
                        <div class="col-12 mb-2" v-for="(product,index) in selectedProducts">
                            <div class="product-info p-2">
                                <div>
                                    <span>  @{{ product.name }}  <small>[TK.@{{ product.price }} x @{{ product.quantity }}]</small></span>
                                    <span class="float-right">TK.@{{ product.total }}</span>
                                </div>
                                <div class="row ml-1 mr-1 mt-1" style="font-size: small">
                                    <div class="col-6 d-flex align-items-center" style="gap: 6px">
                                        <button type="button" class="qty-btn" @click="updateQuantity(product, 'sub')">−</button>
                                        <input type="number" class="form-control" style="height: 28px;font-size: x-small"  min="1" :max="product.stock" v-model="product.quantity" @keyup="updateQuantity(product, 'false')">
                                        <button type="button" class="qty-btn" @click="updateQuantity(product, 'add')">+</button>
                                    </div>
                                    <div class="col-6 input-group" style="gap: 5px">
                                        <input type="text" class="form-control"
                                               aria-label="Text input with dropdown button" style="height: 28px;font-size: x-small"  v-model="product.discountValue" @keyup="updateProductDiscount(product)" :disabled="!product.discountable">
                                        <div class="input-group-append">
                                            <select name="" id="" class="form-control"  style="height: 28px;font-size: x-small"  v-model="product.discountType" @change="updateProductDiscount(product)" :disabled="!product.discountable">
                                                <option value="">Off</option>
                                                <option value="p">%</option>
                                                <option value="f">TK</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-sm btn-danger" @click="delete_selected_product(product)">X</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <ul class="pos-totals">
                        <li><span>Subtotal</span><span>TK.@{{ total_bill }}</span></li>
                        <li><span>Products Discount</span><span>TK.@{{ productWiseDiscount }}</span></li>

                        <li><span>Coupon Discount</span><span><strong><span
                                        v-if="couponCodeDiscountShowValue">( @{{ couponCodeDiscountShowValue }} )</span>@{{ couponCodeDiscountAmount ?? 'N/A' }}</strong></span>
                        </li>
                        <li><span>Overall Discount</span><span>TK.@{{ total_discount_amount }}</span></li>
                        <li><span>Special Discount</span><span>TK.@{{ special_discount_amount }}</span></li>
                        <li><span>Membership Discount (@{{customer && customer.purchase_discount > 0 ? customer.purchase_discount +'% @ '+customer.minimum_purchase + 'TK' : ''}} )</span><span>TK.@{{ membership_discount_amount }}</span></li>
                        <li><span>Total Discount</span><span>TK.@{{ allDiscountAmount }}</span></li>
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
                        <h5>Pay now</h5>
                        <span>@{{ total_items }} Items</span>
                        <span class="float-right">TK. @{{ total_payable_bill }} >></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <b-modal ref="coupon-modal" hide-footer title="Apply Coupon">
        <div class="d-block text-center">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" placeholder="Enter Coupon Code" v-model="couponCode"
                           class="form-control">
                </div>
                <div class="col-md-6">
                    <input type="text" placeholder="Enter OTP" v-model="otp"
                           class="form-control">
                </div>
            </div>
        </div>
        <b-button class="mt-3" variant="outline-danger" @click="closeCouponModal">Close</b-button>
        <b-button class="mt-3" variant="outline-info" :disabled="couponCode.length < 1 || otp.length < 4"
                  v-on:click="getCouponDiscountValue">Apply
        </b-button>
        <b-button class="mt-3 float-right" variant="outline-success" :disabled="couponCode.length < 1 || customerNumber.length < 10"
                  v-on:click="sendOtpToCustomer">Send OTP
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
            <div class="row">
                <div class="col-md-6">
                    <input type="number" min="0" step="0.01" placeholder="Enter Discount Amount" v-model="total_discount_value"
                           class="form-control">
                </div>
                <div class="col-md-6">
                    <input type="text" placeholder="Enter OTP" v-model="otp"
                           class="form-control">
                </div>
            </div>

        </div>
        <b-button class="mt-3" variant="outline-danger" @click="closeDiscountModal">Close</b-button>
        <b-button class="mt-3" variant="outline-info" v-on:click="updateDiscount">
            Apply
        </b-button>
        <b-button class="mt-3 float-right" variant="outline-success" :disabled="total_discount_value == 0 || total_discount_type.length < 1"
                  v-on:click="sendRegularDiscountOtpToCustomer">Send OTP
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
                            @foreach(salePaymentMethodOptions() as $method)
                            <option value="{{ $method['value'] }}">{{ $method['label'] }}</option>
                            @endforeach
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
