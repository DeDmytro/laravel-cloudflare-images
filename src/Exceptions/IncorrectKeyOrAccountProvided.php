<?php

namespace DeDmytro\CloudflareImages\Exceptions;

class IncorrectKeyOrAccountProvided extends CloudflareImagesApiException
{
    public function __construct()
    {
        parent::__construct(
            'Incorrect CloudFlare Key Or Account ID provided. Visit the Github page for more information.'
        );
    }
}
