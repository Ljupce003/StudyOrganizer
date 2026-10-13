<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ProxyHeadersTest extends TestCase
{
    public function test_trusted_proxy_preserves_public_https_urls_and_client_ip(): void
    {
        config(['trustedproxy.proxies' => ['172.20.0.2']]);
        $this->registerProbe();

        $this->withServerVariables($this->forwardedRequest('172.20.0.2'))
            ->get('/proxy-probe')
            ->assertOk()
            ->assertJson([
                'secure' => true,
                'url' => 'https://studyorganizer.example.com/proxy-probe',
                'ip' => '203.0.113.10',
                'port' => 443,
            ]);
    }

    public function test_untrusted_request_cannot_spoof_forwarding_headers(): void
    {
        config(['trustedproxy.proxies' => ['172.20.0.2']]);
        $this->registerProbe();

        $this->withServerVariables($this->forwardedRequest('203.0.113.99'))
            ->get('/proxy-probe')
            ->assertOk()
            ->assertJson([
                'secure' => false,
                'url' => 'http://localhost/proxy-probe',
                'ip' => '203.0.113.99',
                'port' => 80,
            ]);
    }

    private function registerProbe(): void
    {
        Route::get('/proxy-probe', function (Request $request) {
            return response()->json([
                'secure' => $request->isSecure(),
                'url' => $request->url(),
                'ip' => $request->ip(),
                'port' => $request->getPort(),
            ]);
        });
    }

    private function forwardedRequest(string $remoteAddress): array
    {
        return [
            'REMOTE_ADDR' => $remoteAddress,
            'HTTP_HOST' => 'localhost',
            'SERVER_PORT' => 80,
            'HTTPS' => 'off',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.10',
            'HTTP_X_FORWARDED_HOST' => 'studyorganizer.example.com',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT' => '443',
        ];
    }
}
