<?php

namespace DeDmytro\CloudflareImages;

use DeDmytro\CloudflareImages\Http\Clients\ImagesApiClient;
use DeDmytro\CloudflareImages\Http\Clients\ImagesVariantsApiClient;

class CloudflareImages
{
    /**
     * Return Images Api Client
     *
     * @return ImagesApiClient
     */
    final public function api(): ImagesApiClient
    {
        return new ImagesApiClient();
    }

    /**
     * Return Variants Api Client
     *
     * @return ImagesVariantsApiClient
     */
    final public function variants(): ImagesVariantsApiClient
    {
        return new ImagesVariantsApiClient();
    }
}
