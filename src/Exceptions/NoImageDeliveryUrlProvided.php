<?php

namespace DeDmytro\CloudflareImages\Exceptions;

class NoImageDeliveryUrlProvided extends CloudflareImagesApiException
{
    public function __construct()
    {
        parent::__construct(
            'No Image Delivery url provided. Please add CLOUDFLARE_IMAGE_DELIVERY_URL to your .env or add to config.'
        );
    }
}
