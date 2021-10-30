<?php

namespace DeDmytro\CloudflareImages;

use DeDmytro\CloudflareImages\Http\Clients\ImagesApiClient;

class CloudflareImages
{
    /**
     * Return Api Client
     *
     * @return ImagesApiClient
     */
    final public function api(): ImagesApiClient
    {
        return new ImagesApiClient();
    }
}
