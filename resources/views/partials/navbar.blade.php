<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}" class="nav-link">{{ getSettingValue('software_name') ? getSettingValue('software_name') : config('app.name') }}</a>
        </li>
        @if(\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id)
        <li class="nav-item d-none d-md-inline-block">
            <span class="erp-nav-chip"><i class="fas fa-store"></i> {{ optional(optional(auth()->user()->employee)->outlet)->name ?? 'Outlet' }}</span>
        </li>
        <li class="nav-item">
            <a href="/pos" class="nav-link erp-nav-pos"><i class="fas fa-cash-register"></i> POS</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{route('pre-orders.index')}}" class="nav-link">Pre Order</a>
        </li>
        @endif
        @if(\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->factory_id)
            <li class="nav-item d-none d-md-inline-block">
                <span class="erp-nav-chip"><i class="fas fa-industry"></i> {{ optional(optional(auth()->user()->employee)->factory)->name ?? 'Factory' }}</span>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{route('pre-orders.index')}}" class="nav-link">Pre Order</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{route('today.requisitions')}}" class="nav-link">Today Requisitions</a>
            </li>
        @endif
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item dropdown">
            <div class="user-panel d-flex" data-toggle="dropdown">
                <div class="image">
                    @if (isset(Auth::guard('web')->user()->employee->image) && file_exists('upload/'.Auth::guard('web')->user()->employee->image))
                            <img src="{{ asset('/upload/'.Auth::guard('web')->user()->employee->image) }}" class="img-circle elevation-2"
                            alt="User Image" style="height: 40px">
                    @else
                        <img src="{{ asset('admin/app-assets/dummy/dammy.jpg') }}"
                        class="img-circle elevation-2" alt="User Image">
                    @endif
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                </div>
            </div>

            <div class="dropdown-menu dropdown-menu dropdown-menu-right mt-2">
                <a href="{{ route('profile') }}" class="dropdown-item"><i class="fa fa-user"></i> Profile</a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                                                this.closest('form').submit();" id="logout-button"><i class="fas fa-power-off"></i> Logout
                    </a>
                </form>
            </div>
        </li>
    </ul>
</nav>
