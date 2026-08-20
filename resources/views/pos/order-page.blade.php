<div id="order" class="pos-pane">
    <div class="row">
        <div class="col-6">
            <div class="pos-pane-list">
                <div class="d-flex align-items-center mb-3">
                    <h5 class="mb-0 mr-2">Orders</h5>
                    <input type="text" class="form-control" placeholder="Search invoice number" @keyup="debounceOrderSearch" v-model="orderInvoiceNumber">
                </div>
                <div style="max-height: calc(100vh - 180px); overflow-y: auto">
                    <div v-if="ordersLoading && orders.length < 1" class="text-muted p-3">Loading orders…</div>
                    <div v-else-if="orders.length < 1" class="text-muted p-3">No orders found.</div>
                    <div class="customerInfo" v-else v-for="(row,index) in orders" :key="row.id" @click="addToSelectedInvoice(row)">
                        <div class="row">
                            <div class="col-8">
                                <span class="customerName">#@{{ row.invoice_number }}</span><br>
                                <span class="text-muted">@{{ row.readable_sell_date_time }}</span><br>
                                <span>@{{ row.customer && row.customer.type == 'regular' ? (row.customer.name + ' - ' + row.customer.mobile) : 'Walking customer' }}</span>
                            </div>
                            <div class="col-4 text-right">
                                <span class="customerName">TK. @{{ row.grand_total }}</span><br>
                                <span class="text-muted">@{{ row.items_sum_quantity }} item(s)</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-2" v-if="ordersLoading && orders.length > 0">Loading…</div>
                    <div class="text-center mt-2" v-else-if="ordersHasMore">
                        <button type="button" class="btn btn-sm new-button" @click="loadMoreOrders">Load more</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="pos-pane-detail">
                <h5 class="mb-3">Order</h5>
                <div v-if="!selectedInvoice.id" class="text-muted">Select an order to view and reprint.</div>
                <div v-else>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="invoiceNumber">#@{{ selectedInvoice.invoice_number }}</span>
                        <span>@{{ selectedInvoice.customer && selectedInvoice.customer.type == 'regular' ? (selectedInvoice.customer.name + ' - ' + selectedInvoice.customer.mobile) : 'Walking customer' }}</span>
                    </div>
                    <div style="max-height: 46vh; overflow-y: auto;">
                        <div class="product-info p-2 mt-2" v-if="selectedInvoice.items && selectedInvoice.items.length > 0" v-for="(row,index) in selectedInvoice.items" :key="index">
                            <span class="productName">@{{ row.coi.name }}</span><br>
                            <span>TK.@{{ row.unit_price }} × @{{ row.quantity }}</span>
                            <span class="float-right">TK.@{{ row.unit_price * row.quantity }}</span>
                        </div>
                    </div>
                    <ul class="pos-totals grand mt-3">
                        <li><span>Subtotal</span><span>TK.@{{ selectedInvoice.subtotal }}</span></li>
                        <li><span>Discount</span><span>TK.@{{ selectedInvoice.discount }}</span></li>
                        <li><span>Total</span><span>TK.@{{ selectedInvoice.grand_total }}</span></li>
                    </ul>
                    <button class="btn saveButton mt-2" @click="printInvoice(selectedInvoice.id)">Print invoice</button>
                </div>
            </div>
        </div>
    </div>
</div>
