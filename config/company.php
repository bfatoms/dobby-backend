<?php

return [
    'company_name' => env('COMPANY_NAME'),
    'frontend_url' => env('FRONTEND_URL'),
    'support_url' => env('SUPPORT_URL'),
    'livechat_url' => env('LIVECHAT_URL'),
    'support_email' => env('SUPPORT_EMAIL'),
    'help_docs' => env('HELP_DOCS'),
    'modules' => [
        'users',
        'permissions',
        'customers',
        'suppliers',
        'products',
        'inventories',
        'sales',
        'purchases',
        'accounts',
        'projects'
    ],
    'due_date_types' => [
        'of the following month',
        'day(s) after order date',
        'day(s) after the end of the order month',
        'of the current month'
    ],
    'tax_types' => [
        'tax inclusive', 'tax exclusive', 'no tax'
    ],
    'ORDER_TYPES' => [
        'SO' => [
            'DELETE_STATUS' => 'DELETED',
            'ALLOWED_HARD_DELETE' => ['DRAFT'],
            'ALLOWED_STATUS_CREATE' =>  ['DRAFT', 'FOR_APPROVAL', 'APPROVED'],
            'SETTING_PREFIX' => 'sales_order_prefix',
            'SETTING_NUMBER' => 'sales_order_next_number',
            'STATUSES' => ["DRAFT", "FOR_APPROVAL", "APPROVED", "DELETED", "INVOICED"],
            'DISALLOWED_UPDATE' => ['DELETED', 'INVOICED'],
            'ALLOWED_STATUS_UPDATE' => [
                'DRAFT' => ['FOR_APPROVAL', 'APPROVED'],
                'FOR_APPROVAL' => ['APPROVED', 'DELETED'],
                'APPROVED' => ['INVOICED', 'DELETED'],
                'INVOICED' => ['APPROVED'],
                'DELETED' => []
            ]
        ],
        "INV" => [
            'DELETE_STATUS' => 'VOID',
            'ALLOWED_HARD_DELETE' => ['DRAFT', 'FOR_APPROVAL'],
            'ALLOWED_STATUS_CREATE' =>  ['DRAFT', 'FOR_APPROVAL', 'APPROVED'],
            'SETTING_PREFIX' => 'invoice_prefix',
            'SETTING_NUMBER' => 'invoice_next_number',
            'STATUSES' => ["DRAFT", "FOR_APPROVAL", "APPROVED", "VOID", "PAID"],
            "DISALLOWED_UPDATE" => ['APPROVED', 'VOID', 'PAID'],
            "ALLOWED_STATUS_UPDATE" => [
                'DRAFT' => ['FOR_APPROVAL', 'APPROVED'],
                'FOR_APPROVAL' => ['APPROVED', 'VOID'],
                'APPROVED' => ['PAID', 'VOID'],
                'PAID' => ['APPROVED'],
                'VOID' => []
            ]
        ],
        "INV-CN" => [
            'DELETE_STATUS' => 'VOID',
            'ALLOWED_HARD_DELETE' => ['DRAFT', 'FOR_APPROVAL'],
            'ALLOWED_STATUS_CREATE' =>  ['DRAFT', 'FOR_APPROVAL', 'APPROVED'],
            'SETTING_PREFIX' => 'credit_note_prefix',
            'SETTING_NUMBER' => 'invoice_next_number',
            'STATUSES' => ["DRAFT", "FOR_APPROVAL", "APPROVED", "VOID", "PAID"],
            "DISALLOWED_UPDATE" => ['APPROVED', 'VOID', 'PAID'],
            "ALLOWED_STATUS_UPDATE" => [
                'DRAFT' => ['FOR_APPROVAL', 'APPROVED'],
                'FOR_APPROVAL' => ['APPROVED', 'VOID'],
                'APPROVED' => ['PAID', 'VOID'],
                'PAID' => ['APPROVED'],
                'VOID' => []
            ]
        ],
        'PO' => [
            'DELETE_STATUS' => 'DELETED',
            'ALLOWED_HARD_DELETE' => ['DRAFT'],
            'ALLOWED_STATUS_CREATE' =>  ['DRAFT', 'FOR_APPROVAL', 'APPROVED'],
            'SETTING_PREFIX' => 'purchase_order_prefix',
            'SETTING_NUMBER' => 'purchase_order_next_number',
            'STATUSES' => ["DRAFT", "FOR_APPROVAL", "APPROVED", "DELETED", "BILLED"],
            'DISALLOWED_UPDATE' => ['DELETED', 'BILLED'],
            'ALLOWED_STATUS_UPDATE' => [
                'DRAFT' => ['FOR_APPROVAL', 'APPROVED'],
                'FOR_APPROVAL' => ['APPROVED', 'DELETED'],
                'APPROVED' => ['BILLED', 'DELETED'],
                'BILLED' => ['APPROVED'],
                'DELETED' => []
            ]
        ],
        'BILL' => [
            'DELETE_STATUS' => 'VOID',
            'ALLOWED_HARD_DELETE' => ['DRAFT', 'FOR_APPROVAL'],
            'ALLOWED_STATUS_CREATE' =>  ['DRAFT', 'FOR_APPROVAL', 'APPROVED'],
            'SETTING_PREFIX' => '',
            'SETTING_NUMBER' => '',
            'STATUSES' => ["DRAFT", "FOR_APPROVAL", "APPROVED", "VOID", "PAID"],
            'DISALLOWED_UPDATE' => ['APPROVED', 'VOID', 'PAID'],
            'ALLOWED_STATUS_UPDATE' => [
                'DRAFT' => ['FOR_APPROVAL', 'APPROVED'],
                'FOR_APPROVAL' => ['APPROVED', 'VOID'],
                'APPROVED' => ['PAID', 'VOID'],
                'PAID' => ['APPROVED'],
                'VOID' => []
            ]
        ],
        'BILL-CN' => [
            'DELETE_STATUS' => 'VOID',
            'ALLOWED_HARD_DELETE' => ['DRAFT', 'FOR_APPROVAL'],
            'ALLOWED_STATUS_CREATE' =>  ['DRAFT', 'FOR_APPROVAL', 'APPROVED'],
            'SETTING_PREFIX' => '',
            'SETTING_NUMBER' => '',
            'STATUSES' => ["DRAFT", "FOR_APPROVAL", "APPROVED", "VOID", "PAID"],
            'DISALLOWED_UPDATE' => ['APPROVED', 'VOID', 'PAID'],
            'ALLOWED_STATUS_UPDATE' => [
                'DRAFT' => ['FOR_APPROVAL', 'APPROVED'],
                'FOR_APPROVAL' => ['APPROVED', 'VOID'],
                'APPROVED' => ['PAID', 'VOID'],
                'PAID' => ['APPROVED'],
                'VOID' => []
            ]
        ],
        'QU' => [
            'DELETE_STATUS' => 'DELETED',
            'ALLOWED_HARD_DELETE' => ['DRAFT'],
            'ALLOWED_STATUS_CREATE' =>  ['DRAFT', 'SENT'],
            'SETTING_PREFIX' => 'quote_prefix',
            'SETTING_NUMBER' => 'quote_next_number',
            'STATUSES' => ["DRAFT", "SENT", "ACCEPTED", "DECLINED", "DELETED" . "SALES"],
            'DISALLOWED_UPDATE' => ['ACCEPTED', 'DECLINED', 'DELETED', 'SALES'],
            'ALLOWED_STATUS_UPDATE' => [
                'DRAFT' => ['SENT'],
                'SENT' => ['ACCEPTED', 'DECLINED', 'DELETED', 'SALES'],
                'ACCEPTED' => ['SENT', 'DELETED', 'SALES'],
                'DECLINED' => ['SENT', 'DELETED'],
                'SALES' => ['SENT'],
                'DELETED' => []
            ]
        ],
        'RMD' => [
            'DELETE_STATUS' => 'VOID',
            'ALLOWED_HARD_DELETE' => [],
            'ALLOWED_STATUS_CREATE' =>  ['APPROVED'],
            'SETTING_PREFIX' => '',
            'SETTING_NUMBER' => '',
            'STATUSES' => ["APPROVED", "VOID" . "PAID"],
            'DISALLOWED_UPDATE' => ["VOID"],
            'ALLOWED_STATUS_UPDATE' => [
                'APPROVED' => ['PAID', 'VOID'],
                'PAID' => ['APPROVED'],
                'VOID' => []
            ]
        ],
        'RMP' => [
            'DELETE_STATUS' => 'VOID',
            'ALLOWED_HARD_DELETE' => [],
            'ALLOWED_STATUS_CREATE' =>  ['APPROVED'],
            'SETTING_PREFIX' => 'invoice_prefix',
            'SETTING_NUMBER' => 'invoice_next_number',
            'STATUSES' => ["APPROVED", "VOID" . "PAID"],
            'DISALLOWED_UPDATE' => ["VOID"],
            'ALLOWED_STATUS_UPDATE' => [
                'APPROVED' => ['PAID', 'VOID'],
                'PAID' => ['APPROVED'],
                'VOID' => []
            ]
        ],
        'RMO' => [
            'DELETE_STATUS' => 'VOID',
            'ALLOWED_HARD_DELETE' => [],
            'ALLOWED_STATUS_CREATE' =>  ['APPROVED'],
            'SETTING_PREFIX' => '',
            'SETTING_NUMBER' => '',
            'STATUSES' => ["APPROVED", "VOID" . "PAID"],
            'DISALLOWED_UPDATE' => ["VOID"],
            'ALLOWED_STATUS_UPDATE' => [
                'APPROVED' => ['PAID', 'VOID'],
                'PAID' => ['APPROVED'],
                'VOID' => []
            ]
        ],
        'SMD' => [
            'DELETE_STATUS' => 'VOID',
            'ALLOWED_HARD_DELETE' => [],
            'ALLOWED_STATUS_CREATE' =>  ['APPROVED'],
            'SETTING_PREFIX' => '',
            'SETTING_NUMBER' => '',
            'STATUSES' => ["APPROVED", "VOID" . "PAID"],
            'DISALLOWED_UPDATE' => ["VOID"],
            'ALLOWED_STATUS_UPDATE' => [
                'APPROVED' => ['PAID', 'VOID'],
                'PAID' => ['APPROVED'],
                'VOID' => []
            ]
        ],
        'SMP' => [
            'DELETE_STATUS' => 'VOID',
            'ALLOWED_HARD_DELETE' => [],
            'ALLOWED_STATUS_CREATE' =>  ['APPROVED'],
            'SETTING_PREFIX' => '',
            'SETTING_NUMBER' => '',
            'STATUSES' => ["APPROVED", "VOID" . "PAID"],
            'DISALLOWED_UPDATE' => ["VOID"],
            'ALLOWED_STATUS_UPDATE' => [
                'APPROVED' => ['PAID', 'VOID'],
                'PAID' => ['APPROVED'],
                'VOID' => []
            ]
        ],
        'SMO' => [
            'DELETE_STATUS' => 'VOID',
            'ALLOWED_HARD_DELETE' => [],
            'ALLOWED_STATUS_CREATE' =>  ['APPROVED'],
            'SETTING_PREFIX' => '',
            'SETTING_NUMBER' => '',
            'STATUSES' => ["APPROVED", "VOID" . "PAID"],
            'DISALLOWED_UPDATE' => ["VOID"],
            'ALLOWED_STATUS_UPDATE' => [
                'APPROVED' => ['PAID', 'VOID'],
                'PAID' => ['APPROVED'],
                'VOID' => []
            ]
        ]
    ],
    'project_statuses' => [
        'DRAFT',
        'IN_PROGRESS',
        'CLOSED'
    ],
    'task_charge_types' => [
        'HOURLY_RATE',
        'FIXED_RATE',
        'NON_CHARGEABLE'
    ],
    'task_statuses' => [
        'ON_GOING',
        'INVOICED',
        'CLOSED'
    ],
    'time_entry_types' => [
        'DURATION',
        'START_END'
    ],
    'expense_charge_types' => [
        'MARK_UP',
        'PASS_COST_ALONG',
        'CUSTOM_PRICE',
        'NON_CHARGEABLE'
    ],
    'project_invoice_rate_types' => [
        'task_rate',
        'employee_rate'
    ],
    // 'project_invoice_description_fields' => [
    //     'project_name',
    //     'task_name',
    //     'employee_name',
    //     'time_entry_date',
    //     'description',
    // ],
];
