<?php

namespace Tests\Unit;

use DeDmytro\CloudflareImages\Facades\CloudflareImages;
use DeDmytro\CloudflareImages\Http\Responses\DetailsResponse;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApiTest extends TestCase
{
    public function testSuccessfulListResponse()
    {
        $result = CloudflareImages::api()->list();

        $this->assertInstanceOf(ListResponse::class, $result);
    }

    public function testSuccessfulUpload()
    {
        $path = dirname(__DIR__) . '/data/images/summer.jpg';

        $file = basename($path);

        $response = CloudflareImages::api()->upload($path);
        $this->assertInstanceOf(DetailsResponse::class, $response);
        $this->assertEquals($file, $response->result->filename);
    }
}
