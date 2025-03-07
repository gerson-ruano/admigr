<?php

return [
    'paths' => ['api/*', 'storage/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // Permitir todas las URLs de origen
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'supports_credentials' => false,
];
