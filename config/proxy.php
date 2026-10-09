<?php

$trustedProxies = trim((string) env('TRUSTED_PROXIES', ''));

return [
    // Keep this in config so proxy trust also works with artisan config:cache.
    'trusted_proxies' => $trustedProxies === '*'
        ? '*'
        : array_values(array_filter(array_map('trim', explode(',', $trustedProxies)))),
];
