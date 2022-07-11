<?php

namespace DeDmytro\CloudflareImages\Exceptions;

use Exception;

class IncorrectKeyOrAccountProvided extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'Incorrect CloudFlare Key Or Account ID provided. Please check CLOUDFLARE_IMAGES_ACCOUNT and CLOUDFLARE_IMAGES_KEY to your .env'
        );
    }
}
