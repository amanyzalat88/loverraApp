<?php 

return [

    'upload' => [
        'profile' => [
            'default' => '/images/profile_default.jpg',
            'dir' => 'profile/',
            'view_path' => '/storage/profile/',
            'upload_path' => 'storage/profile/'
        ],
        'company' => [
            'default' => '/images/logo_invoice_print.png',
            'dir' => 'company/',
            'view_path' => '/storage/company/',
            'upload_path' => 'storage/company/'
        ],
        'imports' => [
            'default' => '',
            'dir' => 'imports/',
            'view_path' => '/storage/imports/',
            'upload_path' => 'storage/imports/',
            'user_format' => 'excel_formats/import/user_format.xls',
            'store_format' => 'excel_formats/import/store_format.xls',
            'supplier_format' => 'excel_formats/import/supplier_format.xls',
            'category_format' => 'excel_formats/import/category_format.xls',
            'product_format' => 'excel_formats/import/product_format.xls'
        ],
        'updates' => [
            'default' => '',
            'dir' => 'updates/',
            'view_path' => '/storage/updates/',
            'upload_path' => 'storage/updates/',
            'user_format' => 'excel_formats/update/user_format.xls',
            'store_format' => 'excel_formats/update/store_format.xls',
            'supplier_format' => 'excel_formats/update/supplier_format.xls',
            'category_format' => 'excel_formats/update/category_format.xls',
            'product_format' => 'excel_formats/update/product_format.xls'
        ],
        'barcode' => [
            'default' => '',
            'dir' => 'barcode/',
            'view_path' => '/storage/barcode/',
            'upload_path' => 'storage/barcode/'
        ],
        'reports' => [
            'default' => '',
            'dir' => 'reports/',
            'view_path' => '/storage/reports/',
            'upload_path' => 'storage/reports/'
        ],
    ],
    
    'unique_code_start' => [
        'user'     => 100,
        'role'     => 100,
        'order'    => 100,
        'category' => 100,
        'supplier' => 100,
    ],

    'demo_notification' => 'This demo version will reset all the data every hour! In case if you got logged out during a session, please login again and continue browsing our demo version'

];