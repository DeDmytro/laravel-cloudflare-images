<?php

namespace DeDmytro\CloudflareImages\Facades;

use DeDmytro\CloudflareImages\Http\Clients\ImagesApiClient;
use DeDmytro\CloudflareImages\Http\Clients\ImagesVariantsApiClient;
use Illuminate\Support\Facades\Facade;

/**
 * @method ImagesApiClient api()
 * @method ImagesVariantsApiClient variants()
 * @mixin \DeDmytro\CloudflareImages\CloudflareImages
 */
class CloudflareImages extends Facade
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
