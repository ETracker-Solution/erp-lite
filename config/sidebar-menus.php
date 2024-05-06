<?php

return [
    'menus' => [
        [
            'name' => 'Dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'url' => '/dashboard',
            'active_condition' => '',
            'has_child' => false,
        ],
        [
            'name' => 'Data Admin Module',
            'icon' => 'fas fa-wrench',
            'active_condition' => false,
            'has_child' => true,
            'child' => [
                [
                    'name' => 'Inventory Settings',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'Inventory Item List',
                            'url' => '/chart-of-inventory',
                            'active_condition' => '',
                        ]
                    ],
                ]
            ],
        ],
        [
            'name' => 'System Admin Module',
            'icon' => 'fas fa-cog',
            'active_condition' => false,
            'has_child' => true,
            'child' => [
                [
                    'name' => 'Users',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'Create New User',
                            'url' => '#',
                            'active_condition' => false,
                        ]
                    ],
                ]
            ],
        ]
    ],
    'logo' => [
        'src' => 'assets/dist/img/AdminLTELogo.png',
        'alt' => 'Logo',
        'text' => 'ERP Lite',
        'url' => '/dashboard'
    ],
    'second_level_icon' => 'fa fa-folder-open',
    'third_level_icon' => 'fa fa-circle'
];
