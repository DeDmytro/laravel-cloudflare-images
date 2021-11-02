<?php

namespace Tests\Unit;

use DeDmytro\CloudflareImages\Facades\CloudflareApi;
use DeDmytro\CloudflareImages\Http\Responses\DetailsResponse;
use DeDmytro\CloudflareImages\Http\Responses\DirectUploadResponse;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ImagesApiTest extends TestCase
{
    public function testSuccessfulListResponse()
    {
        $result = CloudflareApi::images()->list();

        $this->assertInstanceOf(ListResponse::class, $result);
    }

    public function testSuccessfulUpload()
    {
        $path = dirname(__DIR__) . '/data/images/summer.jpg';

        $file = basename($path);

        $response = CloudflareApi::images()->upload($path);

        $this->assertInstanceOf(DetailsResponse::class, $response);
        $this->assertEquals($file, $response->result->filename);

        $response = CloudflareApi::images()->get($response->result->id);
        $this->assertInstanceOf(DetailsResponse::class, $response);

        $response = CloudflareApi::images()->delete($response->result->id);
        $this->assertInstanceOf(DetailsResponse::class, $response);
    }

    public function testDirectUploadResponse()
    {
        $response = CloudflareApi::images()->directUploadUrl();

        $this->assertInstanceOf(DetailsResponse::class, $response);
        $this->assertNotNull($response->result->uploadURL);
        $this->assertNotNull($response->result->id);
    }
}
