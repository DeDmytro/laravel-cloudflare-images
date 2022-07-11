<?php

namespace DeDmytro\CloudflareImages\Exceptions;

use Exception;

class CloudflareImageNotFound extends Exception
{
    public function __construct(?string $imageId = null)
    {
        parent::__construct(
            "Cloudflare image($imageId) not found"
        );
    }
}
