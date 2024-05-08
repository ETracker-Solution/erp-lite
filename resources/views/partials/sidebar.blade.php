<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{config('sidebar-menus.logo.url')}}" class="brand-link">
        <img src="{{asset(config('sidebar-menus.logo.src'))}}" alt="{{ config('sidebar-menus.logo.alt') }}"
             class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('sidebar-menus.logo.text') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @foreach(config('sidebar-menus.menus') as $mainMenu)
                    <li class="nav-item {{ $mainMenu['has_child'] &&  $mainMenu['active_condition'] ? 'menu-open' : ''}}">
                        <a href="{{ !$mainMenu['has_child'] ? $mainMenu['url'] : '#'}}"
                           class="nav-link {{ $mainMenu['active_condition'] ? 'active' : '' }}">
                            <i class="nav-icon {{ $mainMenu['icon'] }}"></i>
                            <p>
                                {{ $mainMenu['name'] }}
                                @if($mainMenu['has_child'])
                                    <i class="right fas fa-angle-left"></i>
                                @endif
                            </p>
                        </a>
                        @if($mainMenu['has_child'])
                            <ul class="nav nav-treeview">
                                @foreach($mainMenu['child'] as $level2Menu)
                                    <li class="nav-item {{ $level2Menu['has_child'] &&  $level2Menu['active_condition'] ? 'menu-open' : ''}}">
                                        <a href="{{ !$level2Menu['has_child'] ? $level2Menu['url'] : '#'}}"
                                           class="nav-link  {{ $level2Menu['active_condition'] ? 'active' : '' }}">
                                            <i class="nav-icon {{ $level2Menu['has_child']? config('sidebar-menus.second_level_icon') :  config('sidebar-menus.third_level_icon')  }} "></i>
                                            <p>
                                                {{$level2Menu['name']}}
                                                @if($level2Menu['has_child'])
                                                    <i class="right fas fa-angle-left"></i>
                                                @endif
                                            </p>
                                        </a>
                                        @if($level2Menu['has_child'])
                                            <ul class="nav nav-treeview">
                                                @foreach($level2Menu['child'] as $level3Menu)
                                                    <li class="nav-item">
                                                        <a href="{{ $level3Menu['url']}}" class="nav-link {{ $level3Menu['active_condition'] ? 'active' : ''  }}">
                                                            <i class="nav-icon  {{config('sidebar-menus.third_level_icon')}}"></i>
                                                            <p>
                                                                {{$level3Menu['name']}}
                                                            </p>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
            @endforeach
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
