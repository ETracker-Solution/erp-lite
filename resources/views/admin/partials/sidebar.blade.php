<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{url('/admin/dashboard')}}" class="brand-link">
        <img src="{{asset('assets')}}/dist/img/AdminLTELogo.png" alt="AdminLTE Logo"
             class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{route('admin.admin_dashboard')}}"
                       class="nav-link {{ (Request::segment(1) == 'dashboard' )?' active':''}}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            General Accounts
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Received Voucher</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Payment Voucher</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Journal Voucher</p>
                            </a>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li class="nav-item {{ (Request::segment(1) == 'categories'|| Request::segment(1) == 'attributes'|| Request::segment(1) == 'brands'||Request::segment(1) == 'units'||Request::segment(1) == 'products' )?'menu-open':''}}">
                    <a href="#"
                       class="nav-link {{ (Request::segment(1) == 'categories'|| Request::segment(1) == 'brands'||Request::segment(1) == 'units'||Request::segment(1) == 'products' )?' active':''}}">
                        <i class="nav-icon fa fas fa-cubes" aria-hidden="true"></i>
                        <p>
                            Products
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('products.index')}}"
                               class="nav-link {{ (Request::segment(1) == 'products' )?' active':''}}">
                                <i class="fas fa-hand-point-right" style="padding-left: 20px"></i>
                                <p>Product</p>
                            </a>
                        </li>  <li class="nav-item">
                            <a href="{{route('attributes.index')}}"
                               class="nav-link {{ (Request::segment(1) == 'attributes' )?' active':''}}">
                                <i class="fas fa-hand-point-right" style="padding-left: 20px"></i>
                                <p>Attribute</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('categories.index')}}"
                               class="nav-link {{ (Request::segment(1) == 'categories' )?' active':''}}">
                                <i class="fas fa-hand-point-right" style="padding-left: 20px"></i>
                                <p>Category</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('brands.index')}}"
                               class="nav-link {{ (Request::segment(1) == 'brands' )?' active':''}}">
                                <i class="fas fa-hand-point-right" style="padding-left: 20px"></i>
                                <p>Brand</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('units.index')}}"
                               class="nav-link {{ (Request::segment(1) == 'units' )?' active':''}}">
                                <i class="fas fa-hand-point-right" style="padding-left: 20px"></i>
                                <p>Unit</p>
                            </a>
                        </li>

                    </ul>
                </li>
                <li class="nav-item {{ (Request::segment(1) == 'suppliers'|| Request::segment(1) == 'purchases' )?'menu-open':''}}">
                    <a href="#"
                       class="nav-link {{ (Request::segment(1) == 'suppliers'||Request::segment(1) == 'purchases' )?' active':''}}">
                        <i class="nav-icon fa fa-suitcase" aria-hidden="true"></i>
                        <p>
                            Purchase
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('suppliers.index')}}"
                               class="nav-link {{ (Request::segment(1) == 'suppliers' )?' active':''}}">
                                <i class="fas fa-hand-point-right" style="padding-left: 20px"></i>
                                <p>Supplier</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('purchases.index')}}"
                               class="nav-link {{ (Request::segment(1) == 'purchases' )?' active':''}}">

                                <i class="fas fa-hand-point-right" style="padding-left: 20px"></i>
                                <p>Purchase</p>
                            </a>
                        </li>

                    </ul>
                </li>
                <li class="nav-item" {{ (Request::segment(1) == 'stocks' )?' active':''}}>
                    <a href="{{route('stocks.index')}}" class="nav-link {{ (Request::segment(1) == 'stocks' )?' active':''}}">
                        <i class="nav-icon fa fas fa-database"></i>
                        <p>
                            Stock
                            <span class="right badge badge-danger">New</span>
                        </p>
                    </a>
                </li>
                <li class="nav-item {{ (Request::segment(1) == 'customers'|| Request::segment(1) == 'sales' )?'menu-open':''}}">
                    <a href="#"
                       class="nav-link {{ (Request::segment(1) == 'customers'||Request::segment(1) == 'sales' )?' active':''}}">
                        <i class="nav-icon fa fa-shopping-cart" aria-hidden="true"></i>
                        <p>
                            Sale
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('customers.index')}}"
                               class="nav-link {{ (Request::segment(1) == 'customers' )?' active':''}}">
                                <i class="fas fa-hand-point-right" style="padding-left: 20px"></i>
                                <p>Customer</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('sales.index')}}"
                               class="nav-link {{ (Request::segment(1) == 'sales' )?' active':''}}">
                                <i class="fas fa-hand-point-right" style="padding-left: 20px"></i>
                                <p>Sale</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Master Data
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Chart of Accounts</p>
                            </a>
                        </li>
                    </ul>
                </li> --}}

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
