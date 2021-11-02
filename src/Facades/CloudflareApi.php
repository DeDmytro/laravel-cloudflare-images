<?php

namespace DeDmytro\CloudflareImages\Facades;

use DeDmytro\CloudflareImages\Http\Clients\ImagesApiClient;
use DeDmytro\CloudflareImages\Http\Clients\ImagesVariantsApiClient;
use Illuminate\Support\Facades\Facade;

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
}
