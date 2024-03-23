<?php

namespace DeDmytro\CloudflareImages\Exceptions;

class CloudflareImageNotFound extends CloudflareImagesApiException
{
    public function __construct(?string $imageId = null)
    {
        parent::__construct(
            "Cloudflare image($imageId) not found"
        );
    }
}
