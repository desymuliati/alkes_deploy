<?php

return [
    // Default threshold for all products
    'default_threshold' => 100,

    // Specific thresholds for certain product types/units
    'unit_thresholds' => [
        'Box' => 100,
        'Pcs' => 100,
        'Botol' => 100,
        'Galon' => 1,
        'Unit' => 100,                
    ],

    // Specific thresholds for individual products (optional)
    'product_thresholds' => [
        // 'nama-slug-produk-1' => 5,
        // 'nama-slug-produk-2' => 25,
    ],
];