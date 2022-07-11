<?php

namespace DeDmytro\CloudflareImages\Facades;

use DeDmytro\CloudflareImages\Http\Clients\ImagesApiClient;
use DeDmytro\CloudflareImages\Http\Clients\ImagesVariantsApiClient;
use DeDmytro\CloudflareImages\Testing\Fakes\ImagesApiClientFake;
use DeDmytro\CloudflareImages\Testing\Fakes\ImagesVariantsApiClientFake;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Testing\Fakes\QueueFake;

/**
 * @method ImagesApiClient images()
 * @method ImagesVariantsApiClient variants()
 * @mixin \DeDmytro\CloudflareImages\CloudflareApi
 */
class CloudflareApi extends Facade
{
    /**
     * Return facade unique key
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'cloudflareImages';
    }

    /**
     * Replace ClouflareApi::images() and  ClouflareApi::variants()
     *
     * @see \DeDmytro\CloudflareImages\CloudflareApi
     * @return void
     */
    public static function mock()
    {
        self::shouldReceive('images')->andReturn(new ImagesApiClientFake());
        self::shouldReceive('variants')->andReturn(new ImagesVariantsApiClientFake());
    }
}
