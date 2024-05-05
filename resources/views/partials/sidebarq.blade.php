<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('admin.adminHome')}}" class="brand-link">
        <img src="{{ asset('admin/dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Gauwal</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{route('admin.adminHome')}}" class="nav-link {{ (Request::segment(1) == 'home' )?' active-color':''}}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('sidebar.dashboard')}}</p>
                    </a>
                </li>
                <li class="nav-header">Products</li>
                <li class="nav-item">
                    <a href="{{route('category.index')}}" class="nav-link {{ (Request::segment(1) == 'category' )?' active-color':''}}">
                        <i class="nav-icon fa fa-server" aria-hidden="true"></i>
                        <p>Category</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('sub-category.index')}}" class="nav-link {{ (Request::segment(1) == 'sub-category' )?' active-color':''}}">

                        <i class="nav-icon fa fa-globe" aria-hidden="true"></i>
                        <p>sub-category</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('unit.index')}}" class="nav-link {{ (Request::segment(1) == 'unit' )?' active-color':''}}">
                        <i class="nav-icon fa fa-list-alt" aria-hidden="true"></i>
                        <p>Unit</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('product.index')}}" class="nav-link {{ (Request::segment(1) == 'product' )?' active-color':''}}">
                        <i class="nav-icon fa fa-adjust" aria-hidden="true"></i>
                        <p>Product</p>
                    </a>
                </li>



            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>