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
            'name' => 'Accounts Module',
            'icon' => 'fas fa-wrench',
            'active_condition' => false,
            'has_child' => true,
            'child' => [
                [
                    'name' => 'General Accounts',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'Receive Voucher',
                            'url' => '/receive-vouchers',
                            'active_condition' => false,
                        ],
                        [
                            'name' => 'Payment Voucher',
                            'url' => '/payment-vouchers',
                            'active_condition' => false,
                        ],
                        [
                            'name' => 'Journal Voucher',
                            'url' => '/journal-vouchers',
                            'active_condition' => false,
                        ],
                        [
                            'name' => 'FT Voucher',
                            'url' => '/fund-transfer-vouchers',
                            'active_condition' => false,
                        ],
                        [
                            'name' => 'Supplier Voucher',
                            'url' => '/supplier-vouchers',
                            'active_condition' => false,
                        ]
                    ],
                ],
                [

                    'name' => 'Ledger Reports',
                    'icon' => 'fas fa-tachometer-alt',
                    'url' => '/reports/ledger-reports',
                    'active_condition' => '',
                    'has_child' => false,
                ],
                [
                    'name' => 'Financial Statement',
                    'icon' => 'fas fa-tachometer-alt',
                    'url' => '/financial-statements',
                    'active_condition' => '',
                    'has_child' => false,
                ]
            ],
            'permissions' => ['account-admin', 'account-operator', 'account-viewer', 'account-approver']
        ],
        [
            'name' => 'Purchase Module',
            'icon' => 'fas fa-shopping-cart',
            'active_condition' => false,
            'has_child' => true,
            'child' => [
                [
                    'name' => 'Purchase Entry',
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
            'permissions' => ['purchase-admin', 'purchase-operator', 'purchase-viewer', 'purchase-approver']
        ],
        [
            'name' => 'Requisition Module',
            'icon' => 'fas fa-shopping-cart',
            'active_condition' => false,
            'has_child' => true,
            'child' => [
                [
                    'name' => 'Requisition Entry',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'Create New Requisition',
                            'url' => '/requisitions/create',
                            'active_condition' => '',
                        ]
                    ],
                ]
            ],
            'permissions' => ['purchase-admin', 'purchase-operator', 'purchase-viewer', 'purchase-approver']
        ],
        [
            'name' => 'Store RM Module',
            'icon' => 'fas fa-shopping-cart',
            'active_condition' => false,
            'has_child' => true,
            'child' => [
                [
                    'name' => 'Report',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'RM Inventory Report',
                            'url' => '/raw-materials-inventory-report',
                            'active_condition' => '',
                        ]
                    ],
                ]
            ],
            'permissions' => ['store-rm-admin', 'store-rm-operator', 'store-rm-viewer', 'store-rm-approver']
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
            'permissions' => ['production-admin', 'production-operator', 'production-viewer', 'production-approver']
        ],
        [
            'name' => 'Store FG Module',
            'icon' => 'fas fa-shopping-cart',
            'active_condition' => false,
            'has_child' => true,
            'child' => [
                [
                    'name' => 'Store FG Entry',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'FG Inventory Transfer',
                            'url' => '/finish-goods-inventory-transfers/create',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'FG Inventory Adjustment',
                            'url' => '/fg-inventory-adjustments/create',
                            'active_condition' => '',
                        ]
                    ],
                ],
                [
                    'name' => 'Store FG Report',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'FG Inventory Report',
                            'url' => '/finish-goods-inventory-report',
                            'active_condition' => '',
                        ]
                    ],
                ]
            ],
            'permissions' => ['store-rm-admin', 'store-rm-operator', 'store-rm-viewer', 'store-rm-approver']
        ],
        [
            'name' => 'Sales Module',
            'icon' => 'fas fa-shopping-cart',
            'active_condition' => false,
            'has_child' => true,
            'child' => [
                [
                    'name' => 'Sales Entry',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'Sales',
                            'url' => '/sales/create',
                            'active_condition' => '',
                        ]
                    ],
                ]
            ],
            'permissions' => ['purchase-admin', 'purchase-operator', 'purchase-viewer', 'purchase-approver']
        ],
        [
            'name' => 'Loyalty Module',
            'icon' => 'fa fa-trophy',
            'active_condition' => false,
            'has_child' => true,
            'child' => [
                [
                    'name' => 'Loyalty Entry',
                    'url' => '#',
                    'has_child' => true,
                    'active_condition' => false,
                    'child' => [
                        [
                            'name' => 'Earn Point',
                            'url' => '/earn-points',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'Redeem Point',
                            'url' => '/redeem-points',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'Point Setting',
                            'url' => '/member-points',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'Membership',
                            'url' => '/memberships',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'Member Type',
                            'url' => '/member-types',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'Promo Code',
                            'url' => '/promo-codes',
                            'active_condition' => '',
                        ]
                    ],
                ]
            ],
            'permissions' => ['purchase-admin', 'purchase-operator', 'purchase-viewer', 'purchase-approver']
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
                    'name' => 'Purchase Settings',
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
                            'url' => '/general-ledger-opening-balances',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'Raw Materials',
                            'url' => '/raw-materials-opening-balances',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'Finish Goods',
                            'url' => '/finish-goods-opening-balances',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'Customer OB',
                            'url' => '/customer-opening-balances',
                            'active_condition' => '',
                        ],
                        [
                            'name' => 'Supplier OB',
                            'url' => '/supplier-opening-balances',
                            'active_condition' => '',
                        ]
                    ],
                ],
                [
                    'name' => 'Create Factory',
                    'url' => '/factories',
                    'has_child' => false,
                    'active_condition' => false,
                ],
                [
                    'name' => 'Create Outlet',
                    'url' => '/outlets',
                    'has_child' => false,
                    'active_condition' => false,
                ],
            ],
            'permissions' => ['data-admin-admin', 'data-admin-operator', 'data-admin-viewer', 'data-admin-approver']
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
                            'url' => '/users',
                            'active_condition' => false,
                        ],
                        [
                            'name' => 'Employees',
                            'url' => '/employees',
                            'active_condition' => false,
                        ]
                    ],
                ]
            ],
            'permissions' => ['system-admin-admin', 'system-admin-operator', 'system-admin-viewer', 'system-admin-approver']
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
