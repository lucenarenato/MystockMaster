<?php

declare(strict_types=1);

return [

    'basic' => [
        'name'     => 'Básico',
        'price_id' => env('STRIPE_PRICE_BASIC', ''),
        'limits'   => [
            'max_users'      => 3,
            'max_products'   => 100,
            'max_sales'      => 300,
            'max_purchases'  => 300,
            'max_customers'  => 100,
            'max_suppliers'  => 50,
            'max_storage_mb' => 512,
        ],
    ],

    'pro' => [
        'name'     => 'Pro',
        'price_id' => env('STRIPE_PRICE_PRO', ''),
        'limits'   => [
            'max_users'      => 10,
            'max_products'   => 500,
            'max_sales'      => 2000,
            'max_purchases'  => 2000,
            'max_customers'  => 500,
            'max_suppliers'  => 200,
            'max_storage_mb' => 2048,
        ],
    ],

    'enterprise' => [
        'name'     => 'Enterprise',
        'price_id' => env('STRIPE_PRICE_ENTERPRISE', ''),
        'limits'   => null, // null = ilimitado em todos os campos
    ],

];
