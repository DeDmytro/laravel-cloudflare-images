<?php

namespace Tests;

use DeDmytro\CloudflareImages\CloudflareImagesServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [CloudflareImagesServiceProvider::class];
    }
}
