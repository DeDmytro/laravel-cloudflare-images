<?php

namespace Tests;

use DeDmytro\CloudflareImages\CloudflareImagesServiceProvider;
use DeDmytro\CloudflareImages\Facades\CloudflareApi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        CloudflareApi::mock();
    }

    protected function getPackageProviders($app): array
    {
        return [CloudflareImagesServiceProvider::class];
    }
}
