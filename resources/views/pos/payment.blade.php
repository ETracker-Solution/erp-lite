<div class="ddwcpos-pay-tab-wrapper">
    <div class="ddwcpos-pay-summary">
        <div><h3>Total Due</h3><span>TK.@{{ total_due }}</span></div>
        <div><h3>Total Paying</h3><span>TK.@{{ total_paying }}</span></div>
        <div><h3>Pay Left</h3><span>TK.@{{ pay_left }}</span></div>
        <div><h3>Change</h3><span>TK.@{{ cash_change }}</span></div>
    </div>
    <div class="ddwcpo-pay-method-container">
        <div class="ddwcpos-method-row"><h4>Amount</h4><h4>Method</h4></div>
        <div class="ddwcpos-method-row ddwcpos-method-active" v-for="(payment, index) in paymentMethods">
            <input type="number"  v-model="payment.amount" :step="payment.method == 'point' ? 100 : 1" @key.press="checkPointInput">
            <select v-model="payment.method">
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="bkash">Bkash</option>
                <option value="nagad">Nagad</option>
                <option value="point">Redeem Point</option>
            </select>
{{--            <span></span>--}}
            <span role="img" aria-label="delete" tabindex="-1" class="anticon anticon-delete" v-if="index !==0" @click="deletePaymentMethod(payment)">
                <svg viewBox="64 64 896 896" focusable="false" data-icon="delete" width="1em" height="1em" fill="currentColor" aria-hidden="true">
                    <path d="M864 256H736v-80c0-35.3-28.7-64-64-64H352c-35.3 0-64 28.7-64 64v80H160c-17.7 0-32 14.3-32 32v32c0 4.4 3.6 8 8 8h60.4l24.7 523c1.6 34.1 29.8 61 63.9 61h454c34.2 0 62.3-26.8 63.9-61l24.7-523H888c4.4 0 8-3.6 8-8v-32c0-17.7-14.3-32-32-32zm-200 0H360v-72h304v72z"></path>
                </svg></span>
        </div><button @click="addMorePaymentMethod">Add Another Payment Method</button></div>
{{--    <div class="ddwcpos-order-note-container">--}}
{{--        <textarea placeholder="Add Order Note"></textarea>--}}
{{--    </div>--}}
    <div class="ddwcpos-numeric-pay-container">
        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-dropbox" :disabled="pay_left > 0" @click="submitOrder">PAY</button>
            </div>
            <div class="col-md-6">
                <button class="btn" style="background-blend-mode: luminosity" @click="paymentMenuShow = !paymentMenuShow">CANCEL</button>
{{--                <span class="ddwcpos-cancel-button" @click="paymentMenuShow = !paymentMenuShow">Cancel</span>--}}
            </div>
        </div>
    </div>
</div>
