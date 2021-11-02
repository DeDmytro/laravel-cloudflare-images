<?php

namespace DeDmytro\CloudflareImages;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

final class CloudflareImagesServiceProvider extends BaseServiceProvider
{
    /**
     * Boot publishable resources
     */
    public function boot(): void
    {
        $this->bootPublishes();
    }

    /**
     * Register package resources
     */
    public function register(): void
    {
        $this->registerConfig();
        $this->registerFacade();
    }

    /**
     * Boot publishable resources
     */
    protected function bootPublishes(): void
    {
        $this->publishes([
            __DIR__ . '/../config/cloudflare_images.php' => $this->app->configPath('cloudflare_images.php'),
        ], 'config');
    }

    /**
     * Register related config
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/cloudflare_images.php', 'cloudflare_images');
    }

    /**
     * Register related facade
     */
    protected function registerFacade(): void
    {
        $this->app->bind('cloudflareImages', function ($app) {
            return new CloudflareApi();
        });
    }
}
