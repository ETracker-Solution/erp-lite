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
                <div class="pos-actions">
                    <button class="btn discount-button smallFont" @click="openCouponModal">Coupon</button>
                    <button class="btn discount-button smallFont" @click="openDiscountModal" :disabled="selectedNotDiscountableProduct" :style="selectedNotDiscountableProduct ? {cursor: 'not-allowed'} : ''">% Discount</button>
                    <button class="btn discount-button smallFont" @click="addSpecialDiscount" :class="selectedSpecialDiscount ? 'text-danger' : ''"  :disabled="selectedNotDiscountableProduct" :style="selectedNotDiscountableProduct ? {cursor: 'not-allowed'} : ''">@{{ special_discount_value }}% Special</button>
                    <button class="btn pause-button smallFont" @click="openOnHoldModal">On Hold</button>
                </div>
                <div class="payment-button mt-2 p-3" style="cursor: pointer" @click="openPaymentModal">
                    <div>
                        <h5>Pay now</h5>
                        <span>@{{ total_items }} items</span>
                        <span class="float-right">TK. @{{ total_payable_bill }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <b-modal ref="coupon-modal" hide-footer centered title="Apply coupon" modal-class="pos-modal">
        <div class="row">
            <div class="col-md-6">
                <label>Coupon code</label>
                <input type="text" placeholder="Code" v-model="couponCode" class="form-control">
            </div>
            <div class="col-md-6">
                <label>OTP</label>
                <input type="text" placeholder="4-digit OTP" v-model="otp" class="form-control">
            </div>
        </div>
        <p class="pay-hint mb-0 mt-2" v-if="customerNumber.length < 10">Enter a customer phone first to send OTP.</p>
        <div class="pos-modal-actions">
            <button type="button" class="btn pos-btn-danger" @click="closeCouponModal">Close</button>
            <button type="button" class="btn pos-btn-ghost" :disabled="couponCode.length < 1 || customerNumber.length < 10" @click="sendOtpToCustomer">Send OTP</button>
            <button type="button" class="btn pos-btn-primary ml-auto" :disabled="couponCode.length < 1 || otp.length < 4" @click="getCouponDiscountValue">Apply</button>
        </div>
    </b-modal>

    <b-modal ref="discount-modal" hide-footer centered title="Apply discount" modal-class="pos-modal">
        <label>Discount type</label>
        <div class="pos-actions mb-3">
            <button type="button" class="btn discount-type-button" :class="total_discount_type == 'fixed' ? 'selected-discount-type' : ''" @click="setDiscountType('fixed')">Fixed TK</button>
            <button type="button" class="btn discount-type-button" :class="total_discount_type == 'percentage' ? 'selected-discount-type' : ''" @click="setDiscountType('percentage')">Percentage</button>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Amount</label>
                <input type="number" min="0" step="0.01" placeholder="0" v-model="total_discount_value" class="form-control">
            </div>
            <div class="col-md-6">
                <label>OTP</label>
                <input type="text" placeholder="OTP" v-model="otp" class="form-control">
            </div>
        </div>
        <div class="pos-modal-actions">
            <button type="button" class="btn pos-btn-danger" @click="closeDiscountModal">Close</button>
            <button type="button" class="btn pos-btn-ghost" :disabled="total_discount_value == 0 || total_discount_type.length < 1" @click="sendRegularDiscountOtpToCustomer">Send OTP</button>
            <button type="button" class="btn pos-btn-primary ml-auto" @click="updateDiscount">Apply</button>
        </div>
    </b-modal>

    <b-modal ref="payment-modal" hide-footer centered size="lg" title="Collect payment" modal-class="pos-modal">
        <div class="pay-grid">
            <div class="payCard due">
                <div class="pay-label">Due</div>
                <span class="pay-value">TK.@{{ total_due }}</span>
            </div>
            <div class="payCard paying">
                <div class="pay-label">Paying</div>
                <span class="pay-value">TK.@{{ total_paying }}</span>
            </div>
            <div class="payCard left">
                <div class="pay-label">Left</div>
                <span class="pay-value">TK.@{{ pay_left }}</span>
            </div>
            <div class="payCard change">
                <div class="pay-label">Change</div>
                <span class="pay-value">TK.@{{ cash_change }}</span>
            </div>
        </div>
        <div class="pay-row" v-for="(payment, index) in paymentMethods" :key="index">
            <div>
                <label v-if="index === 0">Amount</label>
                <input type="number" class="form-control" v-model="payment.amount" :step="payment.method == 'point' ? 100 : 1">
            </div>
            <div>
                <label v-if="index === 0">Method</label>
                <select class="form-control" v-model="payment.method" @change="checkAvail(index)">
                    @foreach(salePaymentMethodOptions() as $method)
                    <option value="{{ $method['value'] }}">{{ $method['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" class="pay-remove" v-if="index !== 0" @click="deletePaymentMethod(payment)" title="Remove">&times;</button>
        </div>
        <button type="button" class="btn pos-btn-ghost" @click="addMorePaymentMethod">+ Add payment method</button>
        <div class="mt-3">
            <label>Waiter</label>
            <select class="form-control" v-model="waiter_id">
                <option value="">No waiter</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->employee_id.'  -- '. $employee->name }}</option>
                @endforeach
            </select>
        </div>
        <p class="pay-hint" v-if="pay_left > 0">Collect TK.@{{ pay_left }} more to complete payment.</p>
        <div class="pos-modal-actions">
            <button type="button" class="btn pos-btn-muted" @click="closePaymentModal">Cancel</button>
            <button type="button" class="btn pos-btn-primary ml-auto" :disabled="(pay_left > 0) || isDisabled" @click="submitOrder">
                @{{ isDisabled ? 'Saving…' : 'Pay TK.' + total_due }}
            </button>
        </div>
    </b-modal>

    <b-modal ref="on-hold-modal" hide-footer centered title="Hold this bill" modal-class="pos-modal">
        <label>Identifier</label>
        <input type="text" placeholder="Table no. or customer name" v-model="onHoldIdentifier" class="form-control">
        <div class="pos-modal-actions">
            <button type="button" class="btn pos-btn-danger" @click="closeOnHoldModal">Close</button>
            <button type="button" class="btn pos-btn-primary ml-auto" :disabled="selectedProducts.length < 1" @click="storeHoldOrder">Hold bill</button>
        </div>
    </b-modal>

    <b-modal ref="on-hold-order-modal" hide-footer centered title="Held bills" modal-class="pos-modal">
        <div v-if="holdOrders.length < 1" class="text-muted">No held bills.</div>
        <div v-else class="hold-row" v-for="(row, index) in holdOrders" :key="index" @click="addHoldOrderToPos(row)">
            <strong>@{{ row.identifier }}</strong>
            <span>@{{ row.items.length }} items</span>
            <span>Qty @{{ row.total }}</span>
        </div>
        <div class="pos-modal-actions">
            <button type="button" class="btn pos-btn-danger" @click="closeOnHoldOrderModal">Close</button>
        </div>
    </b-modal>

    <b-modal ref="pre-order-modal" hide-footer centered title="Pre-order" modal-class="pos-modal">
        <div class="form-group">
            <label>Delivery date</label>
            <input type="datetime-local" class="form-control" v-model="preOrderValues.delivery_date">
        </div>
        <div class="form-group">
            <label>Order from</label>
            <select class="form-control" v-model="preOrderValues.order_from">
                <option value="outlet">Outlet</option>
                <option value="facebook">Facebook</option>
            </select>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label>Advance</label>
                    <input type="number" placeholder="0" class="form-control" v-model="preOrderValues.advance_payment">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label>Paid by</label>
                    <select class="form-control" v-model="preOrderValues.paid_by">
                        <option value="">Select</option>
                        <option value="cash">Cash</option>
                        <option value="bkash">Bkash</option>
                        <option value="nagad">Nagad</option>
                        <option value="card">Card</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Comments</label>
            <textarea rows="2" class="form-control" v-model="preOrderValues.comment"></textarea>
        </div>
        <div class="pos-modal-actions">
            <button type="button" class="btn pos-btn-danger" @click="closePreOrderModal">Close</button>
            <button type="button" class="btn pos-btn-primary ml-auto" :disabled="selectedProducts.length < 1" @click="storePreOrder()">Submit order</button>
        </div>
    </b-modal>

</template>
