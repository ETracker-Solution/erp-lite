<?php

use Illuminate\Support\Facades\Request;

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
            'name' => 'Procurement Module',
            'icon' => 'fas fa-shopping-cart',
            'active_condition' => false,
            'has_child' => true,
            'child' => [
                [
                    'name' => 'Procurement Entry',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'Goods Purchase Bill',
                            'url' => '/purchases/create',
                            'active_condition' => '',
                        ]
                    ],
                ]
            ],
        ],
        [
            'name' => 'Production Module',
            'icon' => 'fas fa-tag',
            'active_condition' => false,
            'has_child' => true,
            'child' => [
                [
                    'name' => 'Production Entry',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'Create Batch',
                            'url' => '/batches',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'RM Consumption',
                            'url' => '/consumptions/create',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'FG Production',
                            'url' => '/productions/create',
                            'active_condition' => '',
                        ]
                    ],
                ]
            ],
        ],
        [
            'name' => 'Data Admin Module',
            'icon' => 'fas fa-wrench',
            'active_condition' => false,
            'has_child' => true,
            'child' => [
                [
                    'name' => 'Accounts Settings',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'Chart Of Accounts',
                            'url' => '/chart-of-accounts',
                            'active_condition' => "Request::segment(1) == 'chart-of-accounts'",
                        ]
                    ],
                ],
                [
                    'name' => 'Inventory Settings',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'Inventory Item List',
                            'url' => '/chart-of-inventories',
                            'active_condition' => false,
                        ],
                        [
                            'name' => 'Unit List',
                            'url' => '/units',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'Store List',
                            'url' => '/stores',
                            'active_condition' => '',
                        ]
                    ],
                ],
                [
                    'name' => 'Procurement Settings',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'Supplier Group List',
                            'url' => '/supplier-groups',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'Supplier List',
                            'url' => '/suppliers',
                            'active_condition' => '',
                        ]
                    ],
                ],
                [
                    'name' => 'Opening Balance',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'GL Account',
                            'url' => '#',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'Raw Materials',
                            'url' => '#',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'Finish Goods',
                            'url' => '#',
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
