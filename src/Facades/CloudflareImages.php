<?php

namespace DeDmytro\CloudflareImages\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method \DeDmytro\CloudflareImages\Http\Clients\ImagesApiClient api()
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
