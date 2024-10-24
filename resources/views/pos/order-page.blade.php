<div id="order">
    <div class="row">
        <div class="col-6  header-gap" style="background-color: #cbcbcb21">
            <div class="m-2 mt-3 d-flex">
                <h4 style="margin-right: 10px">Orders</h4>
                <input type="text" class="form-control" placeholder="Search Order By ID">
            </div>
            <div class="row" style="margin: 10px; max-height: 100vh; overflow-y: auto">
                <div class="col-12 customerInfo"  v-for="(row,index) in orders" @click="addToSelectedInvoice(row)">
                    <div class="row">
                        <div class="col-8">
                            <span class="customerName">#@{{ row.invoice_number }}</span><br>
                            <span>@{{ row.readable_sell_date_time }}</span><br>
                            <span>   @{{ row.customer && row.customer.type == 'regular' ? (row.customer.name + ' - ' + row.customer.mobile) : 'Walking Customer' }}</span>
                        </div>
                        <div class="col-4" style="align-content: center">
                            <span class="customerName">TK. @{{ row.grand_total }}</span><br>
                            <span>@{{ row.items_sum_quantity }} Item(s)</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-6">
            <div class="mb-2">
                <h3>Order Info </h3>
                <div>
                    <span class="invoiceNumber">#@{{ selectedInvoice.invoice_number }}</span>
                    <span class="float-right">@{{ selectedInvoice.customer && selectedInvoice.customer.type == 'regular' ? (selectedInvoice.customer.name + ' - ' + selectedInvoice.customer.mobile) : 'Walking Customer' }}</span>
                </div>
                <div class="m-2 mt-4" style="min-height: 50vh">
                    <div class="row" style="max-height: 50vh; overflow-y: auto;">
                        <div class="col-12 product-info mt-2"  v-if="selectedInvoice.items && selectedInvoice.items.length > 0" v-for="(row,index) in selectedInvoice.items">
                            <span class="productName"> @{{ row.coi.name }}</span><br>
                            <span class="productPrice">TK.@{{ row.unit_price }} x @{{ row.quantity }}</span>
                            <span class="float-right productPrice">TK.@{{ row.unit_price * row.quantity }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <hr>
                    <div>
                        <ul style="list-style-type: none">
                            <li><span>Subtotal</span><span style="float: right">TK.@{{ selectedInvoice.subtotal }}</span></li>
                            <li><span>Discount</span><span style="float: right">TK.@{{ selectedInvoice.discount }}</span></li>
                            <li><span>Coupon(s)</span><span style="float: right">N/A</span></li>
                            <li><span>Total</span><span style="float: right">@{{ selectedInvoice.grand_total }}</span></li>
                            <li><span>Cash</span><span style="float: right">TK.0</span></li>
                            <li><span>Change</span><span style="float: right">TK.0</span></li>
                        </ul>
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn saveButton"  @click="printInvoice(selectedInvoice.id)">Print</button>
                </div>
            </div>

        </div>
    </div>
</div>
