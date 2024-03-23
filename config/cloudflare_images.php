<?php

return [
    'account'           => env('CLOUDFLARE_IMAGES_ACCOUNT'),
    'key'               => env('CLOUDFLARE_IMAGES_KEY'),
    'delivery_url'      => env('CLOUDFLARE_IMAGES_DELIVERY_URL'), // https://imagedelivery.net/ZWd9g1K8vvvVv_Yyyy_XXX
    'default_variation' => env('CLOUDFLARE_IMAGES_DEFAULT_VARIATION', 'public'), // One of defined image variation on Cloudlflare Images Variations
    'signature_token'   => env('CLOUDFLARE_IMAGES_SIGNATURE_TOKEN'),
];
