<?php

$trustedProxies = trim((string) env('TRUSTED_PROXIES', ''));

return [
    // Laravel's TrustProxies middleware reads this after HTTP bootstrapping.
    // Keeping it in config also supports artisan config:cache.
    'proxies' => $trustedProxies === '*'
        ? '*'
        : array_values(array_filter(array_map('trim', explode(',', $trustedProxies)))),
];
