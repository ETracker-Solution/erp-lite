<div class="pos-topbar">
    <div class="row align-items-center">
        <div class="col-3 col-md-2 logo">
            <a href="{{ url('/dashboard') }}">
                <img src="{{ asset('upload').'/'.getSettingValue('company_logo') }}" alt="Logo">
            </a>
        </div>
        <div class="col-9 col-md-10 pos-nav text-right">
            <button class="btn new-button" :class="currentActiveMenu == 'home' ? 'active' : ''" type="button" @click="changeToNav('home')">Home</button>
            <button class="btn new-button" :class="currentActiveMenu == 'customers' ? 'active' : ''" type="button" @click="changeToNav('customers')">Customer</button>
            <button class="btn new-button" :class="currentActiveMenu == 'orders' ? 'active' : ''" type="button" @click="changeToNav('orders')">Order</button>
            <button class="btn new-button" type="button" @click="openOnHoldOrderModal">On-Hold</button>
            <a href="{{ url('/dashboard') }}" class="pos-dash-link">Dashboard</a>
        </div>
    </div>
</div>
