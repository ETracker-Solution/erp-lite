<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{url('/dashboard')}}" class="brand-link">
        <img src="{{ asset('upload').'/'.getSettingValue('company_logo') }}" alt="{{ config('sidebar-menus.logo.alt') }}" alt="AdminLTE Logo"
             class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        {{--            <div class="user-panel mt-3 pb-3 mb-3 d-flex">--}}
        {{--                <div class="image">--}}
        {{--                    <img src="{{asset('assets')}}/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">--}}
        {{--                </div>--}}
        {{--                <div class="info">--}}
        {{--                    <a href="#" class="d-block">Alexander Pierce</a>--}}
        {{--                </div>--}}
        {{--            </div>--}}

        <!-- SidebarSearch Form -->
        {{--            <div class="form-inline">--}}
        {{--                <div class="input-group" data-widget="sidebar-search">--}}
        {{--                    <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">--}}
        {{--                    <div class="input-group-append">--}}
        {{--                        <button class="btn btn-sidebar">--}}
        {{--                            <i class="fas fa-search fa-fw"></i>--}}
        {{--                        </button>--}}
        {{--                    </div>--}}
        {{--                </div>--}}
        {{--            </div>--}}

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{route('dashboard')}}"
                       class="nav-link {{ (Request::segment(1) == 'dashboard' )?' active':''}}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                @can('account-admin')
                <li class="nav-item {{ (Request::segment(1) == 'reports'|| Request::segment(1) == 'financial-statements'|| Request::segment(1) == 'receive-vouchers'|| Request::segment(1) == 'payment-vouchers'|| Request::segment(1) == 'journal-vouchers'|| Request::segment(1) == 'fund-transfer-vouchers'|| Request::segment(1) == 'supplier-vouchers' )?'menu-open':''}}">
                    <a href="#" class="nav-link {{ (Request::segment(1) == 'reports'|| Request::segment(1) == 'financial-statements')?' active':''}}">
                        <i class="nav-icon fas fa-wrench"></i>
                        <p>
                            Accounts Module
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{ (Request::segment(1) == 'receive-vouchers'|| Request::segment(1) == 'payment-vouchers'|| Request::segment(1) == 'journal-vouchers'|| Request::segment(1) == 'fund-transfer-vouchers'|| Request::segment(1) == 'supplier-vouchers' )?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'receive-vouchers'|| Request::segment(1) == 'payment-vouchers'||Request::segment(1) == 'journal-vouchers'||Request::segment(1) == 'fund-transfer-vouchers'||Request::segment(1) == 'supplier-vouchers'  )?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    General Accounts
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('receive-vouchers.index')}}" class="nav-link {{ (Request::segment(1) == 'receive-vouchers' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Receive Voucher</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('payment-vouchers.index')}}" class="nav-link {{ (Request::segment(1) == 'payment-vouchers' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Payment Voucher</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('journal-vouchers.index')}}" class="nav-link {{ (Request::segment(1) == 'journal-vouchers' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Journal Voucher</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('fund-transfer-vouchers.index')}}" class="nav-link {{ (Request::segment(1) == 'fund-transfer-vouchers' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>FT Voucher</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('supplier-vouchers.index')}}" class="nav-link {{ (Request::segment(1) == 'supplier-vouchers' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Supplier Voucher</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('ledger.reports') }}" class="nav-link {{ (Request::segment(2) == 'ledger-reports' )?' active':''}}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Leger Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('financial-statements.index') }}" class="nav-link {{ (Request::segment(1) == 'financial-statements' )?' active':''}}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Financial Report</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan
                
                @can('purchase-admin')
                <li class="nav-item {{ (Request::segment(1) == 'purchases' )?'menu-open':''}}">
                    <a href="#" class="nav-link {{ (Request::segment(1) == 'purchases')?' active':''}}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Purchase Module
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{ (Request::segment(1) == 'purchases')?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'purchases')?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Purchase Entry
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('purchases.create')}}" class="nav-link {{ (Request::segment(1) == 'purchases' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Goods Purchase Bill</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @endcan
                
                @can('store-rm-admin')
                    <li class="nav-item {{ (Request::segment(1) == 'rm-inventory-transfers'|| Request::segment(1) == 'rm-inventory-adjustments'|| Request::segment(1) == 'rm-requisitions'|| Request::segment(1) == 'rm-requisition-deliveries'|| Request::segment(1) == 'raw-materials-inventory-report' )?'menu-open':''}}">
                    <a href="#" class="nav-link {{ (Request::segment(1) == 'rm-inventory-transfers'|| Request::segment(1) == 'rm-inventory-adjustments'|| Request::segment(1) == 'rm-requisitions'|| Request::segment(1) == 'rm-requisition-deliveries'|| Request::segment(1) == 'raw-materials-inventory-report')?' active':''}}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Store RM Module
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{ (Request::segment(1) == 'rm-inventory-transfers'|| Request::segment(1) == 'rm-inventory-adjustments' )?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'rm-inventory-transfers'|| Request::segment(1) == 'rm-inventory-adjustments' )?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Store RM Entry
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('rm-inventory-transfers.create')}}" class="nav-link {{ (Request::segment(1) == 'rm-inventory-transfers' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>RM Inventory Transfer</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('rm-inventory-adjustments.create')}}" class="nav-link {{ (Request::segment(1) == 'rm-inventory-adjustments' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>RM Inventory Adjustment</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ (Request::segment(1) == 'rm-requisitions'|| Request::segment(1) == 'rm-requisition-deliveries' )?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'rm-requisitions'|| Request::segment(1) == 'rm-requisition-deliveries'  )?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Store RM Requisition
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('rm-requisitions.create')}}" class="nav-link {{ (Request::segment(1) == 'rm-requisitions' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Create RM Requisition</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('rm-requisition-deliveries.create')}}" class="nav-link {{ (Request::segment(1) == 'rm-requisition-deliveries' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>RM Requisition Delivery</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ (Request::segment(1) == 'raw-materials-inventory-report' )?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'raw-materials-inventory-report'  )?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Store RM Report
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('raw-materials-inventory-report.index')}}" class="nav-link {{ (Request::segment(1) == 'raw-materials-inventory-report' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>RM Inventory Report</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @endcan
                
                @can('production-admin')
                <li class="nav-item {{ (Request::segment(1) == 'consumptions'|| Request::segment(1) == 'batches'|| Request::segment(1) == 'productions' )?'menu-open':''}}">
                    <a href="#" class="nav-link {{ (Request::segment(1) == 'consumptions'|| Request::segment(1) == 'batches'|| Request::segment(1) == 'productions')?' active':''}}">
                        <i class="nav-icon fas fa-tag"></i>
                        <p>
                            Production Module
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{ (Request::segment(1) == 'consumptions' || Request::segment(1) == 'batches'|| Request::segment(1) == 'productions')?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'consumptions'|| Request::segment(1) == 'batches'|| Request::segment(1) == 'productions')?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Production Entry
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('batches.index')}}" class="nav-link {{ (Request::segment(1) == 'batches' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Batch Entry</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('consumptions.create')}}" class="nav-link {{ (Request::segment(1) == 'consumptions' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>RM Consumption</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('productions.create')}}" class="nav-link {{ (Request::segment(1) == 'productions' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>FG Production</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @endcan
                
                @can('store-fg-admin')
                <li class="nav-item {{ (Request::segment(1) == 'fg-inventory-transfers'|| Request::segment(1) == 'fg-inventory-adjustments'|| Request::segment(1) == 'requisitions'|| Request::segment(1) == 'fg-requisition-deliveries'|| Request::segment(1) == 'finish-goods-inventory-report' )?'menu-open':''}}">
                    <a href="#" class="nav-link {{ (Request::segment(1) == 'fg-inventory-transfers'|| Request::segment(1) == 'fg-inventory-adjustments'|| Request::segment(1) == 'requisitions'|| Request::segment(1) == 'fg-requisition-deliveries'|| Request::segment(1) == 'finish-goods-inventory-report')?' active':''}}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Store FG Module
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{ (Request::segment(1) == 'fg-inventory-transfers'|| Request::segment(1) == 'fg-inventory-adjustments' )?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'fg-inventory-transfers'|| Request::segment(1) == 'fg-inventory-adjustments' )?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Store FG Entry
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('fg-inventory-transfers.create')}}" class="nav-link {{ (Request::segment(1) == 'fg-inventory-transfers' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>FG Inventory Transfer</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('fg-inventory-adjustments.create')}}" class="nav-link {{ (Request::segment(1) == 'fg-inventory-adjustments' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>FG Inventory Adjustment</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ (Request::segment(1) == 'requisitions'|| Request::segment(1) == 'fg-requisition-deliveries' )?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'requisitions'|| Request::segment(1) == 'fg-requisition-deliveries'  )?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Store FG Requisition
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('requisitions.create')}}" class="nav-link {{ (Request::segment(1) == 'requisitions' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Create FG Requisition</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('fg-requisition-deliveries.create')}}" class="nav-link {{ (Request::segment(1) == 'fg-requisition-deliveries' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>FG Requisition Delivery</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ (Request::segment(1) == 'finish-goods-inventory-report' )?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'finish-goods-inventory-report'  )?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Store FG Report
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('finish-goods-inventory-report.index')}}" class="nav-link {{ (Request::segment(1) == 'finish-goods-inventory-report' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>FG Inventory Report</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @endcan

                
                @can('sales-admin')
                <li class="nav-item {{ (Request::segment(1) == 'sales'||Request::segment(1) == 'sale-reports' )?'menu-open':''}}">
                    <a href="#" class="nav-link {{ (Request::segment(1) == 'sales'||Request::segment(1) == 'sale-reports')?' active':''}}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Sales Module
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{ (Request::segment(1) == 'sales')?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'sales')?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Sales Entry
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('sales.create')}}" class="nav-link {{ (Request::segment(1) == 'sales' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Sales</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ (Request::segment(1) == 'sale-reports')?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'sale-reports')?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Report
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('sale-reports.index')}}" class="nav-link {{ (Request::segment(1) == 'sale-reports' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Sales Report</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @endcan
                
                @can('loyalty-admin')
                <li class="nav-item {{ (Request::segment(1) == 'earn-points'||Request::segment(1) == 'redeem-points'||Request::segment(1) == 'member-points'||Request::segment(1) == 'memberships'||Request::segment(1) == 'member-types'||Request::segment(1) == 'promo-codes' )?'menu-open':''}}">
                    <a href="#" class="nav-link {{ (Request::segment(1) == 'earn-points'||Request::segment(1) == 'redeem-points'||Request::segment(1) == 'member-points'||Request::segment(1) == 'memberships'||Request::segment(1) == 'member-types'||Request::segment(1) == 'promo-codes')?' active':''}}">
                        <i class="nav-icon fa fa-trophy"></i>
                        <p>
                            Loyalty Module
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{ (Request::segment(1) == 'earn-points'||Request::segment(1) == 'redeem-points'||Request::segment(1) == 'member-points'||Request::segment(1) == 'memberships'||Request::segment(1) == 'member-types'||Request::segment(1) == 'promo-codes')?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'earn-points'||Request::segment(1) == 'redeem-points'||Request::segment(1) == 'member-points'||Request::segment(1) == 'memberships'||Request::segment(1) == 'member-types'||Request::segment(1) == 'promo-codes')?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Loyalty Entry
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('earn-points.index')}}" class="nav-link {{ (Request::segment(1) == 'earn-points' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Earn Point</p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('redeem-points.index')}}" class="nav-link {{ (Request::segment(1) == 'redeem-points' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Redeem Point</p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('member-points.index')}}" class="nav-link {{ (Request::segment(1) == 'member-points' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Point Setting</p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('memberships.index')}}" class="nav-link {{ (Request::segment(1) == 'memberships' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>MemberShip</p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('member-types.index')}}" class="nav-link {{ (Request::segment(1) == 'member-types' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>MemberType</p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('promo-codes.index')}}" class="nav-link {{ (Request::segment(1) == 'promo-codes' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Promo Code</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @endcan
                
                @can('data-admin-admin')
                <li class="nav-item {{ (Request::segment(1) == 'chart-of-accounts'||Request::segment(1) == 'chart-of-inventories'|| Request::segment(1) == 'units' || Request::segment(1) == 'stores'|| Request::segment(1) == 'supplier-groups'|| Request::segment(1) == 'suppliers'|| Request::segment(1) == 'general-ledger-opening-balances'|| Request::segment(1) == 'raw-materials-opening-balances'||Request::segment(1) == 'finish-goods-opening-balances'||Request::segment(1) == 'customer-opening-balances' || Request::segment(1) == 'supplier-opening-balances'||Request::segment(1) == 'factories'||Request::segment(1) == 'outlets'||Request::segment(1) == 'designations'||Request::segment(1) == 'departments' )?'menu-open':''}}">
                    <a href="#" class="nav-link {{ (Request::segment(1) == 'chart-of-accounts'|| Request::segment(1) == 'chart-of-inventories'|| Request::segment(1) == 'units' || Request::segment(1) == 'stores'|| Request::segment(1) == 'supplier-groups'|| Request::segment(1) == 'suppliers'|| Request::segment(1) == 'general-ledger-opening-balances'|| Request::segment(1) == 'raw-materials-opening-balances'||Request::segment(1) == 'finish-goods-opening-balances'||Request::segment(1) == 'customer-opening-balances' || Request::segment(1) == 'supplier-opening-balances'||Request::segment(1) == 'factories'||Request::segment(1) == 'outlets'||Request::segment(1) == 'designations'||Request::segment(1) == 'departments')?' active':''}}">
                        <i class="nav-icon fas fa-wrench"></i>
                        <p>
                            Data Admin Module
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{ (Request::segment(1) == 'chart-of-accounts' )?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'chart-of-accounts' )?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Account Setting
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('chart-of-accounts.index')}}" class="nav-link {{ (Request::segment(1) == 'chart-of-accounts' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Chart Of Accounts</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ (Request::segment(1) == 'chart-of-inventories'|| Request::segment(1) == 'units' || Request::segment(1) == 'stores' )?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'chart-of-inventories'|| Request::segment(1) == 'units' || Request::segment(1) == 'stores'  )?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Inventory Setting
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('chart-of-inventories.index')}}" class="nav-link {{ (Request::segment(1) == 'chart-of-inventories' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Inventory Item List</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('units.index')}}" class="nav-link {{ (Request::segment(1) == 'units' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Unit List</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('stores.index')}}" class="nav-link {{ (Request::segment(1) == 'stores' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Store List</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ (Request::segment(1) == 'supplier-groups'|| Request::segment(1) == 'suppliers' )?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'supplier-groups'|| Request::segment(1) == 'suppliers'  )?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Purchase Setting
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('supplier-groups.index')}}" class="nav-link {{ (Request::segment(1) == 'supplier-groups' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Supplier Group List</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('suppliers.index')}}" class="nav-link {{ (Request::segment(1) == 'suppliers' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Supplier List</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ (Request::segment(1) == 'general-ledger-opening-balances'|| Request::segment(1) == 'raw-materials-opening-balances'||Request::segment(1) == 'finish-goods-opening-balances'||Request::segment(1) == 'customer-opening-balances' || Request::segment(1) == 'supplier-opening-balances')?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'general-ledger-opening-balances'|| Request::segment(1) == 'raw-materials-opening-balances' ||Request::segment(1) == 'finish-goods-opening-balances' ||Request::segment(1) == 'customer-opening-balances' || Request::segment(1) == 'supplier-opening-balances')?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Opening Balance
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('general-ledger-opening-balances.index')}}" class="nav-link {{ (Request::segment(1) == 'general-ledger-opening-balances' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>GL Account</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('raw-materials-opening-balances.index')}}" class="nav-link {{ (Request::segment(1) == 'raw-materials-opening-balances' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Raw Materials</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('finish-goods-opening-balances.index')}}" class="nav-link {{ (Request::segment(1) == 'finish-goods-opening-balances' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Finish Goods</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('customer-opening-balances.index')}}" class="nav-link {{ (Request::segment(1) == 'customer-opening-balances' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Customer OB</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('supplier-opening-balances.index')}}" class="nav-link {{ (Request::segment(1) == 'supplier-opening-balances' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Supplier OB</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('factories.index')}}" class="nav-link {{ (Request::segment(1) == 'factories' )?' active':''}}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Create Factory</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('outlets.index')}}" class="nav-link {{ (Request::segment(1) == 'outlets' )?' active':''}}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Create Outlet</p>
                            </a>
                        </li>
                        <li class="nav-item {{ (Request::segment(1) == 'designations'||Request::segment(1) == 'departments' )?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'designations'||Request::segment(1) == 'departments'  )?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    HR
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('designations.index')}}" class="nav-link {{ (Request::segment(1) == 'designations' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Designation</p>
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('departments.index')}}" class="nav-link {{ (Request::segment(1) == 'departments' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Department</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @endcan
                

                @can('system-admin-admin')
                <li class="nav-item {{ (Request::segment(1) == 'users'||Request::segment(1) == 'employees'||Request::segment(1) == 'outlet-configs'||Request::segment(1) == 'system-settings' )?'menu-open':''}}">
                    <a href="#" class="nav-link {{ (Request::segment(1) == 'users'||Request::segment(1) == 'employees'||Request::segment(1) == 'outlet-configs'||Request::segment(1) == 'system-settings')?' active':''}}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            System Admin Module
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item {{ (Request::segment(1) == 'users'||Request::segment(1) == 'employees')?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'users'||Request::segment(1) == 'employees')?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Users
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('users.index')}}" class="nav-link {{ (Request::segment(1) == 'users' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>User list</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('employees.index')}}" class="nav-link {{ (Request::segment(1) == 'employees' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Employees</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ (Request::segment(1) == 'outlet-configs')?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'outlet-configs')?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    Outlet Config
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('outlet-configs.create')}}" class="nav-link {{ (Request::segment(1) == 'outlet-configs' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Outlet Payment</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ (Request::segment(1) == 'system-settings')?'menu-open':''}}">
                            <a href="#" class="nav-link {{ (Request::segment(1) == 'system-settings')?' active':''}}">
                                <i class="nav-icon fa fa-folder-open"></i>
                                <p>
                                    System Setting
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('system-settings.create')}}" class="nav-link {{ (Request::segment(1) == 'system-settings' )?' active':''}}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>System Setting</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @endcan

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
