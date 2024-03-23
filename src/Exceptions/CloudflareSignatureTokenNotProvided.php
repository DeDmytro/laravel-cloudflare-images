<?php

namespace DeDmytro\CloudflareImages\Exceptions;

class CloudflareSignatureTokenNotProvided extends CloudflareImagesApiException
{
    public function __construct()
    {
        parent::__construct(
            'Cloudflare Signature Token not provided. Visit the Github page for more information.'
        );
    }
}
