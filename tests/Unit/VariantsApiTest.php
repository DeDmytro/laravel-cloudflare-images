<?php

namespace Tests\Unit;

use DeDmytro\CloudflareImages\Facades\CloudflareImages;
use DeDmytro\CloudflareImages\Http\Responses\DetailsResponse;
use DeDmytro\CloudflareImages\Http\Responses\DirectUploadResponse;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VariantsApiTest extends TestCase
{
    public function testSuccessfulListResponse()
    {
        $result = CloudflareImages::variants()->list();

        $this->assertInstanceOf(ListResponse::class, $result);
    }
}