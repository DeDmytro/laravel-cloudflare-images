<?php

namespace DeDmytro\CloudflareImages;

use DeDmytro\CloudflareImages\Exceptions\NoImageDeliveryUrlProvided;
use DeDmytro\CloudflareImages\Http\Clients\ImagesApiClient;
use DeDmytro\CloudflareImages\Http\Clients\ImagesVariantsApiClient;

class CloudflareApi
{
    /**
     * Return Images Api Client
     *
     * @return ImagesApiClient
     */
    final public function images(): ImagesApiClient
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
