<?php

return [
    'types' => [
        'assets' => [
            'current assets',
            'fixed assets',
            'inventory',
            'non-current assets',
            'prepayment',
            'bank'
        ],
        'equity' => [
            'equity'
        ],
        'expenses' => [
            'depreciation',
            'direct cost',
            'expenses',
            'overhead'
        ],
        'liabilities' => [
            'current liabilities',
            'liabilities',
            'non-current liabilities'
        ],
        'revenue' => [
            'other income',
            'revenue'
        ]
    ],
    'default_tax' => [
        [
            'name' => 'Tax Exempt',
            'rate' => 0.0,
            'is_system_tax' => true
        ]
    ],
    'default_account' => [
        [
            'name' => 'Accounts Receivable',
            'code' => '600600',
            'is_system_account' => true,
            'type' => 'current assets',
            'description' => 'Outstanding invoices the company has issued out to the client but has not yet received in cash at balance date.',
            'tax_rate_id' => 1,
            'system_name' => 'accounts-receivable'
        ],
        [
            'name' => 'Accounts Payable',
            'code' => '800100',
            'is_system_account' => true,
            'type' => 'current liabilities',
            'description' => 'Outstanding invoices the company has received from suppliers but has not yet paid at balance date.',
            'tax_rate_id' => 1,
            'system_name' => 'accounts-payable'
        ],
        [
            'name' => 'Sales Tax',
            'code' => '820',
            'is_system_account' => true,
            'type' => 'current liabilities',
            'description' => "The balance in this account represents Sales Tax owing to or from your tax authority. At the end of the tax period, it is this account that should be used to code against either the 'refunds from' or 'payments to' your tax authority that will appear on the bank statement. Dobby has been designed to use only one sales tax account to track sales taxes on income and expenses, so there is no need to add any new sales tax accounts to Dobby.",
            'tax_rate_id' => 1,
            'system_name' => 'sales-tax'
        ]
    ]
];
