<?php

namespace Tests\Unit;

use DeDmytro\CloudflareImages\Facades\CloudflareApi;
use DeDmytro\CloudflareImages\Http\Responses\DetailsResponse;
use DeDmytro\CloudflareImages\Http\Responses\DirectUploadResponse;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class VariantsApiTest extends TestCase
{
    public function testSuccessfulListResponse(): void
    {
        $result = CloudflareApi::variants()->list();

        $this->assertInstanceOf(ListResponse::class, $result);
    }
}
