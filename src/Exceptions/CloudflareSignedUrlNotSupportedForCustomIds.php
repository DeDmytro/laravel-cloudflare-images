<?php

namespace DeDmytro\CloudflareImages\Exceptions;

class CloudflareSignedUrlNotSupportedForCustomIds extends CloudflareImagesApiException
{
    public function __construct()
    {
        parent::__construct(
            'Cloudflare signed URL not supported for custom IDs. Read https://developers.cloudflare.com/images/manage-images/serve-images/serve-private-images/'
        );
    }
}

