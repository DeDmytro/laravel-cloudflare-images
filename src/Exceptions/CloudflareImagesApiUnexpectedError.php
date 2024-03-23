<?php

namespace DeDmytro\CloudflareImages\Exceptions;

class CloudflareImagesApiUnexpectedError extends CloudflareImagesApiException
{
    public function __construct(string $message)
    {
        parent::__construct(
            'Unexpected CloudFlare Images API error: ' . $message
        );
    }
}
