<?php
// this file contains module and permissions
return [
    "organization-settings" => [
        "list", "show", "create", "update", "trash", "assign-permission"
    ],
    "contacts" => [
        "list", "show", "create", "update", "trash",
    ],
    "products" => [
        "list", "show", "create", "update", "trash",
    ],
    'purchases-bills' => [
        "list", "show", "create", "update", "trash", "approve"
    ],
    'sales-invoices' => [
        "list", "show", "create", "update", "trash", "approve"
    ],
    'banks' => [
        "list", "show", "create", "update", "trash", "approve"
    ],
    "projects" => [
        "list", "show", "create", "update", "trash", "all"
    ]
];
