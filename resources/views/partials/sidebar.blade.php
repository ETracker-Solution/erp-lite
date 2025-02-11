<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{url('/dashboard')}}" class="brand-link">
        <img src="{{ asset('upload') . '/' . getSettingValue('company_logo') }}" alt="Logo"
            class="brand-image elevation-3" style="opacity: .8; margin-top: 1px; border-radius: 5px; float: unset">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        {{-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">--}}
            {{-- <div class="image">--}}
                {{-- <img src="{{asset('assets')}}/dist/img/user2-160x160.jpg" class="img-circle elevation-2"
                    alt="User Image">--}}
                {{-- </div>--}}
            {{-- <div class="info">--}}
                {{-- <a href="#" class="d-block">Alexander Pierce</a>--}}
                {{-- </div>--}}
            {{-- </div>--}}

        <!-- SidebarSearch Form -->
        {{-- <div class="form-inline">--}}
            {{-- <div class="input-group" data-widget="sidebar-search">--}}
                {{-- <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">--}}
                {{-- <div class="input-group-append">--}}
                    {{-- <button class="btn btn-sidebar">--}}
                        {{-- <i class="fas fa-search fa-fw"></i>--}}
                        {{-- </button>--}}
                    {{-- </div>--}}
                {{-- </div>--}}
            {{-- </div>--}}

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{route('dashboard')}}"
                        class="nav-link {{ (Request::segment(1) == 'dashboard') ? ' active' : ''}}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                @canany(['accounts-receive-voucher', 'accounts-payment-voucher', 'accounts-journal-voucher', 'accounts-ft-voucher', 'accounts-delivery-cash-transfer', 'accounts-delivery-cash-receive', 'supplier-voucher', 'accounts-ledger-report', 'accounts-financial-report', 'accounts-supplier-voucher'])
                    <li
                        class="nav-item {{ (Request::segment(1) == 'reports' || Request::segment(1) == 'financial-statements' || Request::segment(1) == 'receive-vouchers' || Request::segment(1) == 'payment-vouchers' || Request::segment(1) == 'journal-vouchers' || Request::segment(1) == 'fund-transfer-vouchers' || Request::segment(1) == 'delivery-cash-transfers' || Request::segment(1) == 'delivery-cash-receives' || Request::segment(1) == 'supplier-vouchers') ? 'menu-open' : ''}}">
                        <a href="#"
                            class="nav-link {{ (Request::segment(1) == 'reports' || Request::segment(1) == 'financial-statements' || Request::segment(1) == 'receive-vouchers' || Request::segment(1) == 'payment-vouchers' || Request::segment(1) == 'journal-vouchers' || Request::segment(1) == 'fund-transfer-vouchers' || Request::segment(1) == 'delivery-cash-transfers' || Request::segment(1) == 'delivery-cash-receives' || Request::segment(1) == 'supplier-vouchers') ? ' active' : ''}}">
                            <i class="nav-icon fas fa-wrench"></i>
                            <p>
                                Accounts Module
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview second-child">
                            @canany(['accounts-receive-voucher', 'accounts-payment-voucher', 'accounts-journal-voucher', 'accounts-ft-voucher', 'accounts-delivery-cash-transfer', 'accounts-delivery-cash-receive', 'accounts-supplier-voucher'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'receive-vouchers' || Request::segment(1) == 'payment-vouchers' || Request::segment(1) == 'journal-vouchers' || Request::segment(1) == 'fund-transfer-vouchers' || Request::segment(1) == 'delivery-cash-transfers' || Request::segment(1) == 'delivery-cash-receives' || Request::segment(1) == 'supplier-vouchers') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'receive-vouchers' || Request::segment(1) == 'payment-vouchers' || Request::segment(1) == 'journal-vouchers' || Request::segment(1) == 'fund-transfer-vouchers' || Request::segment(1) == 'delivery-cash-transfers' || Request::segment(1) == 'delivery-cash-receives' || Request::segment(1) == 'supplier-vouchers') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            General Accounts
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('accounts-receive-voucher')
                                            <li class="nav-item">
                                                <a href="{{route('receive-vouchers.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'receive-vouchers') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Receive Voucher</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('accounts-payment-voucher')
                                            <li class="nav-item">
                                                <a href="{{route('payment-vouchers.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'payment-vouchers') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Payment Voucher</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('accounts-journal-voucher')
                                            <li class="nav-item">
                                                <a href="{{route('journal-vouchers.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'journal-vouchers') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Journal Voucher</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('accounts-ft-voucher')
                                            <li class="nav-item">
                                                <a href="{{route('fund-transfer-vouchers.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'fund-transfer-vouchers') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>FT Voucher</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('accounts-supplier-voucher')
                                            <li class="nav-item">
                                                <a href="{{route('supplier-vouchers.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'supplier-vouchers') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Supplier Voucher</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('accounts-delivery-cash-transfer')
                                            <li class="nav-item">
                                                <a href="{{route('delivery-cash-transfers.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'delivery-cash-transfers') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>DC Transfer</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('accounts-delivery-cash-receive')
                                            <li class="nav-item">
                                                <a href="{{route('delivery-cash-receives.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'delivery-cash-receives') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>DC Receive</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('supplier-voucher')
                                            <li class="nav-item">
                                                <a href="{{route('supplier-vouchers.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'supplier-vouchers') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Supplier Voucher</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                            @can('accounts-ledger-report')
                                <li class="nav-item">
                                    <a href="{{ route('ledger.reports') }}"
                                        class="nav-link {{ (Request::segment(2) == 'ledger-reports') ? ' active' : ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Ledger Report</p>
                                    </a>
                                </li>
                            @endcan
                            @can('accounts-financial-report')
                                <li class="nav-item">
                                    <a href="{{ route('financial-statements.index') }}"
                                        class="nav-link {{ (Request::segment(1) == 'financial-statements') ? ' active' : ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Financial Report</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
                @canany(['purchase-goods-purchase-bill', 'purchase-purchase-return-bill'])
                    <li
                        class="nav-item {{ (Request::segment(1) == 'purchases' || Request::segment(1) == 'purchase-returns' || Request::segment(1) == 'fg-purchases') ? 'menu-open' : ''}}">
                        <a href="#"
                            class="nav-link {{Request::segment(1) == 'purchases' || Request::segment(1) == 'purchase-returns' ? 'active' : ''}}">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>
                                Purchase Module
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview second-child">
                            @canany(['purchase-goods-purchase-bill', 'purchase-purchase-return-bill'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'purchases' || Request::segment(1) == 'purchase-returns' || Request::segment(1) == 'fg-purchases') ? 'menu-open' : ''}}">
                                    <a href="#" class="nav-link {{ (Request::segment(1) == 'purchases') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Purchase Entry
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('purchase-goods-purchase-bill')
                                            <li class="nav-item">
                                                <a href="{{route('purchases.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'purchases') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Goods Purchase Bill</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('purchase-purchase-return-bill')
                                            <li class="nav-item">
                                                <a href="{{route('purchase-returns.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'purchase-returns') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Purchase Return Bill</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('purchase-goods-purchase-bill')
                                            <li class="nav-item">
                                                <a href="{{route('fg-purchases.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'fg-purchases') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Finish Goods Purchase</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                            {{-- <li
                                class="nav-item {{ (Request::segment(1) == 'purchases'||Request::segment(1) == 'purchase-returns')?'menu-open':''}}">
                                --}}
                                {{-- <a href="#"
                                    class="nav-link {{ (Request::segment(1) == 'purchases')?' active':''}}">--}}
                                    {{-- <i class="nav-icon fa fa-folder-open"></i>--}}
                                    {{-- <p>--}}
                                        {{-- Report--}}
                                        {{-- <i class="right fas fa-angle-left"></i>--}}
                                        {{-- </p>--}}
                                    {{-- </a>--}}
                                {{-- <ul class="nav nav-treeview third-child">--}}

                                    {{-- <li class="nav-item">--}}
                                        {{-- <a href="{{route('purchase-reports.index')}}" --}} {{--
                                            class="nav-link {{ (Request::segment(1) == 'purchase-reports' )?' active':''}}">--}}
                                            {{-- <i class="far fa-circle nav-icon"></i>--}}
                                            {{-- <p>Purchase Report</p>--}}
                                            {{-- </a>--}}
                                        {{-- </li>--}}

                                    {{-- </ul>--}}
                                {{-- </li>--}}
                        </ul>
                    </li>
                @endcanany
                @canany(['store-rm-rm-inventory-adjustment', 'store-rm-rm-inventory-transfer', 'store-rm-rm-inventory-transfer-receive', 'store-rm-create-rm-requisition', 'store-rm-rm-requisition-delivery', 'store-rm-rm-inventory-report'])
                    <li
                        class="nav-item {{ (Request::segment(1) == 'rm-inventory-transfers' || Request::segment(1) == 'rm-transfer-receives' || Request::segment(1) == 'rm-inventory-adjustments' || Request::segment(1) == 'rm-requisitions' || Request::segment(1) == 'rm-requisition-deliveries' || Request::segment(1) == 'raw-materials-inventory-report') ? 'menu-open' : ''}}">
                        <a href="#"
                            class="nav-link {{ (Request::segment(1) == 'rm-inventory-transfers' || Request::segment(1) == 'rm-transfer-receives' || Request::segment(1) == 'rm-inventory-adjustments' || Request::segment(1) == 'rm-requisitions' || Request::segment(1) == 'rm-requisition-deliveries' || Request::segment(1) == 'raw-materials-inventory-report') ? ' active' : ''}}">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>
                                Store RM Module
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview second-child">
                            @canany(['store-rm-rm-inventory-adjustment', 'store-rm-rm-inventory-transfer', 'store-rm-rm-inventory-transfer-receive'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'rm-inventory-transfers' || Request::segment(1) == 'rm-transfer-receives' || Request::segment(1) == 'rm-inventory-adjustments') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'rm-inventory-transfers' || Request::segment(1) == 'rm-transfer-receives' || Request::segment(1) == 'rm-inventory-adjustments') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Store RM Entry
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('store-rm-rm-inventory-transfer')
                                            <li class="nav-item">
                                                <a href="{{route('rm-inventory-transfers.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'rm-inventory-transfers') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>RM Inventory Transfer</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('store-rm-rm-inventory-transfer-receive')
                                            <li class="nav-item">
                                                <a href="{{route('rm-transfer-receives.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'rm-transfer-receives') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>RM Inventory Transfer Receive</p>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('store-rm-rm-inventory-adjustment')
                                            <li class="nav-item">
                                                <a href="{{route('rm-inventory-adjustments.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'rm-inventory-adjustments') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>RM Inventory Adjustment</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                            @canany(['store-rm-create-rm-requisition', 'store-rm-rm-requisition-delivery'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'rm-requisitions' || Request::segment(1) == 'rm-requisition-deliveries') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'rm-requisitions' || Request::segment(1) == 'rm-requisition-deliveries') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Store RM Requisition
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('store-rm-create-rm-requisition')
                                            <li class="nav-item">
                                                <a href="{{route('rm-requisitions.create')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'rm-requisitions') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Create RM Requisition</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('store-rm-rm-requisition-delivery')
                                            <li class="nav-item">
                                                <a href="{{route('rm-requisition-deliveries.create')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'rm-requisition-deliveries') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>RM Requisition Delivery</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                            @canany(['store-rm-rm-inventory-report'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'raw-materials-inventory-report') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'raw-materials-inventory-report') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Store RM Report
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('store-rm-rm-inventory-report')
                                            <li class="nav-item">
                                                <a href="{{route('raw-materials-inventory-report.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'raw-materials-inventory-report') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>RM Inventory Report</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany
                @canany(['production-batch-entry', 'production-rm-consumption', 'production-fg-production'])
                    <li
                        class="nav-item {{ (Request::segment(1) == 'consumptions' || Request::segment(1) == 'batches' || Request::segment(1) == 'productions') ? 'menu-open' : ''}}">
                        <a href="#"
                            class="nav-link {{ (Request::segment(1) == 'consumptions' || Request::segment(1) == 'batches' || Request::segment(1) == 'productions') ? ' active' : ''}}">
                            <i class="nav-icon fas fa-tag"></i>
                            <p>
                                Production Module
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview second-child">
                            @canany(['production-batch-entry', 'production-rm-consumption', 'production-fg-production'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'consumptions' || Request::segment(1) == 'batches' || Request::segment(1) == 'productions') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'consumptions' || Request::segment(1) == 'batches' || Request::segment(1) == 'productions') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Production Entry
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('production-batch-entry')
                                            <li class="nav-item">
                                                <a href="{{route('batches.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'batches') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Batch</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('production-rm-consumption')
                                            <li class="nav-item">
                                                <a href="{{route('consumptions.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'consumptions') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>RM Consumption</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('production-fg-production')
                                            <li class="nav-item">
                                                <a href="{{route('productions.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'productions') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>FG Production</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany
                @canany(['store-fg-fg-inventory-transfer', 'store-fg-fg-inventory-transfer-receive', 'store-fg-fg-inventory-adjustment', 'store-fg-fg-requisition-list', 'store-fg-fg-requisition-delivery', 'store-fg-fg-inventory-report', 'store-fg-fg-delivery-receive'])
                    <li
                        class="nav-item {{ (Request::segment(1) == 'fg-inventory-transfers' || Request::segment(1) == 'fg-transfer-receives' || Request::segment(1) == 'fg-inventory-adjustments' || Request::segment(1) == 'requisitions' || Request::segment(1) == 'fg-requisition-deliveries' || Request::segment(1) == 'fg-delivery-receives' || Request::segment(1) == 'finish-goods-inventory-report' || Request::segment(1) == 'finish-goods-wastage-report') ? 'menu-open' : ''}}">
                        <a href="#"
                            class="nav-link {{ (Request::segment(1) == 'fg-inventory-transfers' || Request::segment(1) == 'fg-transfer-receives' || Request::segment(1) == 'fg-inventory-adjustments' || Request::segment(1) == 'requisitions' || Request::segment(1) == 'fg-requisition-deliveries' || Request::segment(1) == 'fg-delivery-receives' || Request::segment(1) == 'finish-goods-inventory-report' || Request::segment(1) == 'finish-goods-wastage-report') ? ' active' : ''}}">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>
                                Store FG Module
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview second-child">
                            @canany(['store-fg-fg-inventory-transfer', 'store-fg-fg-inventory-transfer-receive', 'store-fg-fg-inventory-adjustment'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'fg-inventory-transfers' || Request::segment(1) == 'fg-transfer-receives' || Request::segment(1) == 'fg-inventory-adjustments') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'fg-inventory-transfers' || Request::segment(1) == 'fg-transfer-receives' || Request::segment(1) == 'fg-inventory-adjustments') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Store FG Entry
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('store-fg-fg-inventory-transfer')
                                            <li class="nav-item">
                                                <a href="{{route('fg-inventory-transfers.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'fg-inventory-transfers') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>FG Inventory Transfer</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('store-fg-fg-inventory-transfer-receive')
                                            <li class="nav-item">
                                                <a href="{{route('fg-transfer-receives.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'fg-transfer-receives') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>FG Inventory Transfer Receive</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('store-fg-fg-inventory-adjustment')
                                            <li class="nav-item">
                                                <a href="{{route('fg-inventory-adjustments.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'fg-inventory-adjustments') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>FG Inventory Adjustment</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                            @canany(['store-fg-fg-requisition-list', 'store-fg-fg-requisition-delivery', 'store-fg-fg-delivery-receive'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'requisitions' || Request::segment(1) == 'fg-requisition-deliveries' || Request::segment(1) == 'fg-delivery-receives') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'requisitions' || Request::segment(1) == 'fg-requisition-deliveries' || Request::segment(1) == 'fg-delivery-receives') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Store FG Requisition
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview second-child">
                                        @can('store-fg-fg-requisition-list')
                                            <li class="nav-item">
                                                <a href="{{route('requisitions.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'requisitions') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>FG Requisition List</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('store-fg-fg-requisition-delivery')
                                            <li class="nav-item">
                                                <a href="{{route('fg-requisition-deliveries.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'fg-requisition-deliveries') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>FG Requisition Delivery</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('store-fg-fg-delivery-receive')
                                            <li class="nav-item">
                                                <a href="{{route('fg-delivery-receives.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'fg-delivery-receives') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>FG Delivery Receive</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                            @canany(['store-fg-fg-inventory-report'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'finish-goods-inventory-report' || Request::segment(1) == 'finish-goods-wastage-report') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'finish-goods-inventory-report' || Request::segment(1) == 'finish-goods-wastage-report') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Store FG Report
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview second-child">
                                        @can('store-fg-fg-inventory-report')
                                            <li class="nav-item">
                                                <a href="{{route('finish-goods-inventory-report.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'finish-goods-inventory-report') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>FG Inventory Report</p>
                                                </a>
                                            </li>
                                        @endcan
                                        <li class="nav-item">
                                            <a href="{{route('finish-goods-wastage-report.index')}}"
                                                class="nav-link {{ (Request::segment(1) == 'finish-goods-wastage-report') ? ' active' : ''}}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>FG Wastage Report</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany
                @canany(['sales-sales', 'sales-sales-report', 'sales-pre-orders-list', 'sales-pre-order-entry', 'sales-other-outlet-sales', 'sales-sales-delivery', 'sales-sales-exchanges',])
                    <li
                        class="nav-item {{ (Request::segment(1) == 'sales' || Request::segment(1) == 'others-outlet-sales' || Request::segment(1) == 'sales-deliveries' || Request::segment(1) == 'sales-exchanges' || Request::segment(1) == 'sale-reports' || Request::segment(1) == 'pre-orders' || Request::segment(1) == 'sales-returns' || Request::segment(1) == 'sale-deleted-list' || Request::segment(1) == 'sale-deleted-show') ? 'menu-open' : ''}}">
                        <a href="#"
                            class="nav-link {{ (Request::segment(1) == 'sales' || Request::segment(1) == 'others-outlet-sales' || Request::segment(1) == 'sales-deliveries' || Request::segment(1) == 'sales-exchanges' || Request::segment(1) == 'sale-reports' || Request::segment(1) == 'pre-orders' || Request::segment(1) == 'sales-returns' || Request::segment(1) == 'sale-deleted-list' || Request::segment(1) == 'sale-deleted-show') ? ' active' : ''}}">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>
                                Sales Module
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview second-child">
                            @canany(['sales-pre-orders-list', 'sales-pre-order-entry'])
                                <li class="nav-item {{ (Request::segment(1) == 'pre-orders') ? 'menu-open' : ''}}">
                                    <a href="#" class="nav-link {{ (Request::segment(1) == 'pre-orders') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Pre Order
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('sales-pre-orders-list')
                                            <li class="nav-item">
                                                <a href="{{route('pre-orders.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'pre-orders' && Request::segment(2) != 'create') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Pre Order List</p>
                                                </a>
                                            </li>
                                        @endcan
                                        {{-- @can('sales-pre-order-entry')--}}
                                        {{-- <li class="nav-item">--}}
                                            {{-- <a href="{{route('pre-orders.create')}}" --}} {{--
                                                class="nav-link {{ (Request::segment(1) == 'pre-orders' && Request::segment(2) == 'create')?' active':''}}">--}}
                                                {{-- <i class="far fa-circle nav-icon"></i>--}}
                                                {{-- <p>Pre Order Entry</p>--}}
                                                {{-- </a>--}}
                                            {{-- </li>--}}
                                        {{-- @endcan--}}
                                    </ul>
                                </li>
                            @endcanany
                            @canany(['sales-sales', 'sales-other-outlet-sales', 'sales-sales-delivery', 'sales-sales-exchanges'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'sales' || Request::segment(1) == 'others-outlet-sales' || Request::segment(1) == 'sales-exchanges' || Request::segment(1) == 'sales-deliveries' || Request::segment(1) == 'sales-returns' || Request::segment(1) == 'sale-deleted-list' || Request::segment(1) == 'sale-deleted-show') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'sales' || Request::segment(1) == 'others-outlet-sales' || Request::segment(1) == 'sales-exchanges' || Request::segment(1) == 'sales-deliveries' || Request::segment(1) == 'sales-returns' || Request::segment(1) == 'sale-deleted-list' || Request::segment(1) == 'sale-deleted-show') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Sales Entry
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('sales-sales')
                                            <li class="nav-item">
                                                <a href="{{route('sales.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'sales') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Sales</p>
                                                </a>
                                            </li>
                                        @endcan
                                        {{-- @can('sales-other-outlet-sales')--}}
                                        {{-- <li class="nav-item">--}}
                                            {{-- <a href="{{route('others-outlet-sales.index')}}" --}} {{--
                                                class="nav-link {{ (Request::segment(1) == 'others-outlet-sales' )?' active':''}}">--}}
                                                {{-- <i class="far fa-circle nav-icon"></i>--}}
                                                {{-- <p>OO Sales</p>--}}
                                                {{-- </a>--}}
                                            {{-- </li>--}}
                                        {{-- @endcan--}}
                                        @can('sales-sales-delivery')
                                            <li class="nav-item">
                                                <a href="{{route('sales-deliveries.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'sales-deliveries') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Sales Delivery</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('sales-sales-exchanges')
                                            <li class="nav-item">
                                                <a href="{{route('sales-returns.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'sales-returns') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Sales Return</p>
                                                </a>
                                            </li>
                                        @endcan
                                        <li class="nav-item">
                                            <a href="{{route('sale_deleted_list')}}"
                                                class="nav-link {{ (Request::segment(1) == 'sale-deleted-list' || Request::segment(1) == 'sale-deleted-show') ? ' active' : ''}}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Deleted Sale Lists</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcanany

                            @canany(['sales-sales-report'])
                                <li class="nav-item {{ (Request::segment(1) == 'sale-reports') ? 'menu-open' : ''}}">
                                    <a href="#" class="nav-link {{ (Request::segment(1) == 'sale-reports') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Report
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('sales-sales-report')
                                            <li class="nav-item">
                                                <a href="{{route('sale-reports.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'sale-reports') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Sales Report</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany
                @canany(['loyalty-earn-point', 'loyalty-redeem-point', 'loyalty-point-setting', 'loyalty-membership', 'loyalty-membertype', 'loyalty-promo-code'])
                    <li
                        class="nav-item {{ (Request::segment(1) == 'earn-points' || Request::segment(1) == 'redeem-points' || Request::segment(1) == 'member-points' || Request::segment(1) == 'memberships' || Request::segment(1) == 'member-types' || Request::segment(1) == 'promo-codes') ? 'menu-open' : ''}}">
                        <a href="#"
                            class="nav-link {{ (Request::segment(1) == 'earn-points' || Request::segment(1) == 'redeem-points' || Request::segment(1) == 'member-points' || Request::segment(1) == 'memberships' || Request::segment(1) == 'member-types' || Request::segment(1) == 'promo-codes') ? ' active' : ''}}">
                            <i class="nav-icon fa fa-trophy"></i>
                            <p>
                                Loyalty Module
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview second-child">
                            @canany(['loyalty-earn-point', 'loyalty-redeem-point', 'loyalty-point-setting', 'loyalty-membership', 'loyalty-membertype', 'loyalty-promo-code'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'earn-points' || Request::segment(1) == 'redeem-points' || Request::segment(1) == 'member-points' || Request::segment(1) == 'memberships' || Request::segment(1) == 'member-types' || Request::segment(1) == 'promo-codes') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'earn-points' || Request::segment(1) == 'redeem-points' || Request::segment(1) == 'member-points' || Request::segment(1) == 'memberships' || Request::segment(1) == 'member-types' || Request::segment(1) == 'promo-codes') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Loyalty Entry
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('loyalty-earn-point')
                                            <li class="nav-item">
                                                <a href="{{route('earn-points.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'earn-points') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Earn Point</p>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('loyalty-redeem-point')
                                            <li class="nav-item">
                                                <a href="{{route('redeem-points.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'redeem-points') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Redeem Point</p>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('loyalty-point-setting')
                                            <li class="nav-item">
                                                <a href="{{route('member-points.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'member-points') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Point Setting</p>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('loyalty-membership')
                                            <li class="nav-item">
                                                <a href="{{route('memberships.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'memberships') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>MemberShip</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('loyalty-membertype')
                                            <li class="nav-item">
                                                <a href="{{route('member-types.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'member-types') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>MemberType</p>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('loyalty-promo-code')
                                            <li class="nav-item">
                                                <a href="{{route('promo-codes.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'promo-codes') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Promo Code</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany
                @canany(['master-data-create-outlet', 'master-data-create-factory', 'master-data-designation', 'master-data-department', 'master-data-gl-account', 'master-data-raw-metarials', 'master-data-finish-goods', 'master-data-customer-ob', 'master-data-supplier-ob', 'master-data-supplier-group-list', 'master-data-supplier-list', 'master-data-inventory-item-list', 'master-data-unit-list', 'master-data-store-list', 'master-data-chart-of-accounts', 'master-data-outlet-accounts'])
                    <li
                        class="nav-item {{ (Request::segment(1) == 'chart-of-accounts' || Request::segment(1) == 'outlet-accounts' || Request::segment(1) == 'chart-of-inventories' || Request::segment(1) == 'units' || Request::segment(1) == 'stores' || Request::segment(1) == 'supplier-groups' || Request::segment(1) == 'suppliers' || Request::segment(1) == 'general-ledger-opening-balances' || Request::segment(1) == 'raw-materials-opening-balances' || Request::segment(1) == 'finish-goods-opening-balances' || Request::segment(1) == 'customer-opening-balances' || Request::segment(1) == 'supplier-opening-balances' || Request::segment(1) == 'factories' || Request::segment(1) == 'outlets' || Request::segment(1) == 'designations' || Request::segment(1) == 'departments' || Request::segment(1) == 'customers') ? 'menu-open' : ''}}">
                        <a href="#"
                            class="nav-link {{ (Request::segment(1) == 'chart-of-accounts' || Request::segment(1) == 'outlet-accounts' || Request::segment(1) == 'chart-of-inventories' || Request::segment(1) == 'units' || Request::segment(1) == 'stores' || Request::segment(1) == 'supplier-groups' || Request::segment(1) == 'suppliers' || Request::segment(1) == 'general-ledger-opening-balances' || Request::segment(1) == 'raw-materials-opening-balances' || Request::segment(1) == 'finish-goods-opening-balances' || Request::segment(1) == 'customer-opening-balances' || Request::segment(1) == 'supplier-opening-balances' || Request::segment(1) == 'factories' || Request::segment(1) == 'outlets' || Request::segment(1) == 'designations' || Request::segment(1) == 'departments' || Request::segment(1) == 'customers') ? ' active' : ''}}">
                            <i class="nav-icon fas fa-wrench"></i>
                            <p>
                                Master Data
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview second-child">
                            @canany(['master-data-chart-of-accounts', 'master-data-outlet-accounts'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'chart-of-accounts' || Request::segment(1) == 'outlet-accounts') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'chart-of-accounts' || Request::segment(1) == 'outlet-accounts') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Account Setting
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('master-data-chart-of-accounts')
                                            <li class="nav-item">
                                                <a href="{{route('chart-of-accounts.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'chart-of-accounts') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Chart Of Accounts</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('master-data-outlet-accounts')
                                            <li class="nav-item">
                                                <a href="{{route('outlet-accounts.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'outlet-accounts') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Outlet Accounts</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                            @canany(['master-data-inventory-item-list', 'master-data-unit-list', 'master-data-store-list'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'chart-of-inventories' || Request::segment(1) == 'units' || Request::segment(1) == 'stores' || Request::segment(1) == 'alter_units') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'chart-of-inventories' || Request::segment(1) == 'units' || Request::segment(1) == 'stores' || Request::segment(1) == 'alter_units') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Inventory Setting
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('master-data-inventory-item-list')
                                            <li class="nav-item">
                                                <a href="{{route('chart-of-inventories.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'chart-of-inventories') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Inventory Item List</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('master-data-unit-list')
                                            <li class="nav-item">
                                                <a href="{{route('units.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'units') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Unit List</p>
                                                </a>
                                            </li>
                                        @endcan
                                        <li class="nav-item">
                                            <a href="{{route('alter_units.index')}}"
                                                class="nav-link {{ (Request::segment(1) == 'alter-units') ? ' active' : ''}}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Alter Unit List</p>
                                            </a>
                                        </li>
                                        @can('master-data-store-list')
                                            <li class="nav-item">
                                                <a href="{{route('stores.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'stores') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Store List</p>
                                                </a>
                                            </li>
                                        @endcan
                                        <li class="nav-item">
                                            <a href="{{route('recipes.index')}}"
                                                class="nav-link {{ (Request::segment(1) == 'recipes') ? ' active' : ''}}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Recipes</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcanany
                            @canany(['master-data-supplier-group-list', 'master-data-supplier-list'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'supplier-groups' || Request::segment(1) == 'suppliers') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'supplier-groups' || Request::segment(1) == 'suppliers') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Purchase Setting
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('master-data-supplier-group-list')
                                            <li class="nav-item">
                                                <a href="{{route('supplier-groups.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'supplier-groups') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Supplier Group List</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('master-data-supplier-list')
                                            <li class="nav-item">
                                                <a href="{{route('suppliers.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'suppliers') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Supplier List</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany

                            @canany(['master-data-gl-account', 'master-data-raw-metarials', 'master-data-finish-goods', 'master-data-customer-ob', 'master-data-supplier-ob'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'general-ledger-opening-balances' || Request::segment(1) == 'raw-materials-opening-balances' || Request::segment(1) == 'finish-goods-opening-balances' || Request::segment(1) == 'customer-opening-balances' || Request::segment(1) == 'supplier-opening-balances') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'general-ledger-opening-balances' || Request::segment(1) == 'raw-materials-opening-balances' || Request::segment(1) == 'finish-goods-opening-balances' || Request::segment(1) == 'customer-opening-balances' || Request::segment(1) == 'supplier-opening-balances') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Opening Balance
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('master-data-gl-account')
                                            <li class="nav-item">
                                                <a href="{{route('general-ledger-opening-balances.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'general-ledger-opening-balances') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>GL Account</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('master-data-raw-metarials')
                                            <li class="nav-item">
                                                <a href="{{route('raw-materials-opening-balances.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'raw-materials-opening-balances') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Raw Materials</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('master-data-finish-goods')
                                            <li class="nav-item">
                                                <a href="{{route('finish-goods-opening-balances.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'finish-goods-opening-balances') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Finish Goods</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('master-data-customer-ob')
                                            <li class="nav-item">
                                                <a href="{{route('customer-opening-balances.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'customer-opening-balances') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Customer OB</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('master-data-supplier-ob')
                                            <li class="nav-item">
                                                <a href="{{route('supplier-opening-balances.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'supplier-opening-balances') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Supplier OB</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                            @canany(['master-data-designation', 'master-data-department'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'designations' || Request::segment(1) == 'departments') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'designations' || Request::segment(1) == 'departments') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            HR
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('master-data-designation')
                                            <li class="nav-item">
                                                <a href="{{route('designations.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'designations') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Designation</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('master-data-department')
                                            <li class="nav-item">
                                                <a href="{{route('departments.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'departments') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Department</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                            @can('master-data-create-factory')
                                <li class="nav-item">
                                    <a href="{{route('factories.index')}}"
                                        class="nav-link {{ (Request::segment(1) == 'factories') ? ' active' : ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Factory</p>
                                    </a>
                                </li>
                            @endcan

                            @can('master-data-create-outlet')
                                <li class="nav-item">
                                    <a href="{{route('outlets.index')}}"
                                        class="nav-link {{ (Request::segment(1) == 'outlets') ? ' active' : ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Outlet List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('master-data-create-customer')
                                <li class="nav-item">
                                    <a href="{{route('customers.index')}}"
                                        class="nav-link {{ (Request::segment(1) == 'customers') ? ' active' : ''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Customer list</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
                @canany(['system-admin-user-list', 'system-admin-employees', 'system-admin-outlet-payment', 'system-admin-system-setting', 'system-admin-system-config'])
                    <li
                        class="nav-item {{ (Request::segment(1) == 'users' || Request::segment(1) == 'employees' || Request::segment(1) == 'outlet-configs' || Request::segment(1) == 'system-settings' || Request::segment(1) == 'system-config') ? 'menu-open' : ''}}">
                        <a href="#"
                            class="nav-link {{ (Request::segment(1) == 'users' || Request::segment(1) == 'employees' || Request::segment(1) == 'outlet-configs' || Request::segment(1) == 'system-settings' || Request::segment(1) == 'system-config') ? ' active' : ''}}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                System Admin Module
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview second-child">
                            @canany(['system-admin-user-list', 'system-admin-employees'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'users' || Request::segment(1) == 'employees') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'users' || Request::segment(1) == 'employees') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Users
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('system-admin-user-list')
                                            <li class="nav-item">
                                                <a href="{{route('users.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'users') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>User list</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('system-admin-employees')
                                            <li class="nav-item">
                                                <a href="{{route('employees.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'employees') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Employee List</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                            @canany(['system-admin-outlet-payment'])
                                <li class="nav-item {{ (Request::segment(1) == 'outlet-configs') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'outlet-configs') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            Outlet Config
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('system-admin-outlet-payment')
                                            <li class="nav-item">
                                                <a href="{{route('outlet-configs.create')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'outlet-configs') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Outlet Payment</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany

                            @canany(['system-admin-system-setting', 'system-admin-system-config'])
                                <li
                                    class="nav-item {{ (Request::segment(1) == 'system-settings' || Request::segment(1) == 'system-config') ? 'menu-open' : ''}}">
                                    <a href="#"
                                        class="nav-link {{ (Request::segment(1) == 'system-settings' || Request::segment(1) == 'system-config') ? ' active' : ''}}">
                                        <i class="nav-icon fa fa-folder-open"></i>
                                        <p>
                                            System Setting
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview third-child">
                                        @can('system-admin-system-setting')
                                            <li class="nav-item">
                                                <a href="{{route('system-settings.create')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'system-settings') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>System Setting</p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('system-admin-system-config')
                                            <li class="nav-item">
                                                <a href="{{route('system-config.index')}}"
                                                    class="nav-link {{ (Request::segment(1) == 'system-config') ? ' active' : ''}}">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>System Config</p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>