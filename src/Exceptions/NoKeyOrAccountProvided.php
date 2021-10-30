<?php

namespace DeDmytro\CloudflareImages\Exceptions;

class NoKeyOrAccountProvided extends \Exception
{
    public function __construct()
    {
        parent::__construct(
            'No CloudFlare Key Or Account ID provided. Please add CLOUDFLARE_IMAGES_ACCOUNT and CLOUDFLARE_IMAGES_KEY to your .env'
        );
    }
}
