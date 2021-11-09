<?php

namespace Tests\Unit;

use Closure;
use DeDmytro\CloudflareImages\Facades\CloudflareApi;
use DeDmytro\CloudflareImages\Http\Responses\DetailsResponse;
use DeDmytro\CloudflareImages\Http\Responses\DirectUploadResponse;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
        $this->uploadedImageAssertions(function (DetailsResponse $response) {
            $response = CloudflareApi::images()->get($response->result->id);
            $this->assertInstanceOf(DetailsResponse::class, $response);
        });
    }

    public function testDirectUploadResponse()
    {
        $response = CloudflareApi::images()->directUploadUrl();

        $this->assertInstanceOf(DetailsResponse::class, $response);
        $this->assertNotNull($response->result->uploadURL);
        $this->assertNotNull($response->result->id);
    }

    public function testImageExists()
    {
        $response = CloudflareApi::images()->exists(Str::random());

        $this->assertEquals(false, $response);

        $this->uploadedImageAssertions(function (DetailsResponse $response) {
            $response = CloudflareApi::images()->exists($response->result->id);
            $this->assertEquals(true, $response);
        });
    }

    /**
     * Upload image and delete it. Allow to assert smth for real image
     *
     * @param  \Closure  $closure
     *
     * @throws \Throwable
     */
    private function uploadedImageAssertions(Closure $closure)
    {
        $path = dirname(__DIR__) . '/data/images/summer.jpg';
        $file = basename($path);

        $response = CloudflareApi::images()->upload($path);

        $this->assertInstanceOf(DetailsResponse::class, $response);
        $this->assertEquals($file, $response->result->filename);

        $closure($response);

        $response = CloudflareApi::images()->delete($response->result->id);
        $this->assertInstanceOf(DetailsResponse::class, $response);
    }
}
