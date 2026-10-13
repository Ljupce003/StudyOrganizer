<?php

namespace Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Contracts\Http\Kernel;
use PHPUnit\Framework\TestCase;

class HttpBootstrapTest extends TestCase
{
    public function test_http_kernel_resolves_before_configuration_is_loaded(): void
    {
        $previousContainer = Container::getInstance();

        try {
            $app = require dirname(__DIR__, 2).'/bootstrap/app.php';

            $this->assertFalse($app->bound('config'));
            // public/index.php resolves the kernel before running bootstrappers.
            $this->assertInstanceOf(Kernel::class, $app->make(Kernel::class));
        } finally {
            Container::setInstance($previousContainer);
        }
    }
}
