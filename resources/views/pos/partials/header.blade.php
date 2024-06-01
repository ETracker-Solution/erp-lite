<div class="header"  style="position: absolute; z-index: 1; width: 50%">
    <div class="" >
        <div class="text-center">
            <div class="row">
                <div class="col-4">
                    <img src="{{ asset('upload').'/'.getSettingValue('company_logo') }}" alt="Cake Town Logo" width="70%" height="55px">
                </div>
                <div class="col-8">
                    <div class="m-2 text-center">
                        <button class="btn new-button"  :class="currentActiveMenu == 'home' ? 'active' : ''" href="#" @click="changeToNav('home')">Home</button>
                        <button class="btn new-button" :class="currentActiveMenu == 'customers' ? 'active' : ''" href="#" @click="changeToNav('customers')">Customer</button>
                        <button class="btn new-button" :class="currentActiveMenu == 'orders' ? 'active' : ''" href="#" @click="changeToNav('orders')">Order</button>
                        <button class="btn new-button" :class="currentActiveMenu == 'pre_orders' ? 'active' : ''" href="#" @click="changeToNav('pre_orders')">Pre-Order</button>
                        <button class="btn new-button"  @click="openOnHoldOrderModal">On-Hold</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
