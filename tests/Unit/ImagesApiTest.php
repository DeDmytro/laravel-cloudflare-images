<?php

namespace Tests\Unit;

use Closure;
use DeDmytro\CloudflareImages\Exceptions\CloudflareSignedUrlNotSupportedForCustomIds;
use DeDmytro\CloudflareImages\Facades\CloudflareApi;
use DeDmytro\CloudflareImages\Http\Responses\DetailsResponse;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use Illuminate\Support\Str;
use Tests\TestCase;
use Throwable;

final class ImagesApiTest extends TestCase
{
    public function testSuccessfulListResponse(): void
    {
        $result = CloudflareApi::images()->list();

        $this->assertInstanceOf(ListResponse::class, $result);
    }

    public function testSuccessfulUpload(): void
    {
        $this->uploadedImageAssertions(function (DetailsResponse $response) {
            $response = CloudflareApi::images()->get($response->result->id);
            $this->assertInstanceOf(DetailsResponse::class, $response);
        });
    }

    public function testDirectUploadResponse(): void
    {
        $response = CloudflareApi::images()->directUploadUrl();

        $this->assertInstanceOf(DetailsResponse::class, $response);
        $this->assertNotNull($response->result->uploadURL);
        $this->assertNotNull($response->result->id);
    }

    public function testImageExists(): void
    {
        $response = CloudflareApi::images()->exists(Str::random());

        $this->assertEquals(false, $response);

        $this->uploadedImageAssertions(function (DetailsResponse $response) {
            $response = CloudflareApi::images()->exists($response->result->id);
            $this->assertEquals(true, $response);
        });
    }

    // list() tests

    public function testListReturnsDefaultOf50Images(): void
    {
        $result = CloudflareApi::images()->list();

        $this->assertInstanceOf(ListResponse::class, $result);
        $this->assertCount(50, $result->result);
    }

    public function testListWithCustomPerPage(): void
    {
        $result = CloudflareApi::images()->list(1, 3);

        $this->assertInstanceOf(ListResponse::class, $result);
        $this->assertCount(3, $result->result);
    }

    // upload() parameter tests

    public function testUploadWithCustomFilename(): void
    {
        $path = dirname(__DIR__) . '/data/images/summer.jpg';
        $customFilename = 'my-custom-name.jpg';

        $response = CloudflareApi::images()->upload($path, $customFilename);

        $this->assertInstanceOf(DetailsResponse::class, $response);
        $this->assertEquals($customFilename, $response->result->filename);

        CloudflareApi::images()->delete($response->result->id);
    }

    public function testUploadWithMetadata(): void
    {
        $path = dirname(__DIR__) . '/data/images/summer.jpg';

        $response = CloudflareApi::images()->upload($path, '', false, ['source' => 'test']);

        $this->assertInstanceOf(DetailsResponse::class, $response);

        CloudflareApi::images()->delete($response->result->id);
    }

    public function testUploadWithRequiredSignedUrl(): void
    {
        $path = dirname(__DIR__) . '/data/images/summer.jpg';

        $response = CloudflareApi::images()->upload($path, '', true);

        $this->assertInstanceOf(DetailsResponse::class, $response);
        $this->assertTrue($response->result->requireSignedURLs);

        CloudflareApi::images()->delete($response->result->id);
    }

    public function testUploadWithCustomId(): void
    {
        $path = dirname(__DIR__) . '/data/images/summer.jpg';
        $customId = 'custom-' . Str::random(8);

        $response = CloudflareApi::images()->upload($path, '', false, [], $customId);

        $this->assertInstanceOf(DetailsResponse::class, $response);
        $this->assertEquals($customId, $response->result->id);

        CloudflareApi::images()->delete($response->result->id);
    }

    public function testUploadThrowsWhenCustomIdAndSignedUrl(): void
    {
        $this->expectException(CloudflareSignedUrlNotSupportedForCustomIds::class);

        $path = dirname(__DIR__) . '/data/images/summer.jpg';
        CloudflareApi::images()->upload($path, '', true, [], 'some-custom-id');
    }

    // uploadViaUrl() tests

    public function testUploadedImageViaURL(): void
    {
        $this->uploadedImageViaURLAssertions(function (DetailsResponse $response) {
            $fetched = CloudflareApi::images()->get($response->result->id);
            $this->assertInstanceOf(DetailsResponse::class, $fetched);
        });
    }

    public function testUploadViaUrlWithMetadata(): void
    {
        $url = 'https://placehold.co/400';

        $response = CloudflareApi::images()->uploadViaUrl($url, ['key' => 'value']);

        $this->assertInstanceOf(DetailsResponse::class, $response);

        CloudflareApi::images()->delete($response->result->id);
    }

    public function testUploadViaUrlWithRequiredSignedUrl(): void
    {
        $url = 'https://placehold.co/400';

        $response = CloudflareApi::images()->uploadViaUrl($url, [], true);

        $this->assertInstanceOf(DetailsResponse::class, $response);
        $this->assertTrue($response->result->requireSignedURLs);

        CloudflareApi::images()->delete($response->result->id);
    }

    // clone() tests

    public function testCloneReturnsDetailsResponse(): void
    {
        $this->uploadedImageAssertions(function (DetailsResponse $original) {
            $clone = CloudflareApi::images()->clone($original->result->id);

            $this->assertInstanceOf(DetailsResponse::class, $clone);

            CloudflareApi::images()->delete($clone->result->id);
        });
    }

    public function testCloneHasDistinctId(): void
    {
        $this->uploadedImageAssertions(function (DetailsResponse $original) {
            $clone = CloudflareApi::images()->clone($original->result->id);

            $this->assertNotEquals($original->result->id, $clone->result->id);

            CloudflareApi::images()->delete($clone->result->id);
        });
    }

    // directUploadUrl() parameter tests

    public function testDirectUploadWithMetadata(): void
    {
        $response = CloudflareApi::images()->directUploadUrl(['tag' => 'test']);

        $this->assertInstanceOf(DetailsResponse::class, $response);
        $this->assertNotNull($response->result->uploadURL);
        $this->assertNotNull($response->result->id);
    }

    public function testDirectUploadWithRequiredSignedUrl(): void
    {
        $response = CloudflareApi::images()->directUploadUrl([], true);

        $this->assertInstanceOf(DetailsResponse::class, $response);
        $this->assertNotNull($response->result->uploadURL);
    }

    // exists() tests

    public function testExistsFalseForUnknownId(): void
    {
        $this->assertFalse(CloudflareApi::images()->exists(Str::random()));
    }

    public function testExistsTrueAfterUpload(): void
    {
        $path = dirname(__DIR__) . '/data/images/summer.jpg';
        $response = CloudflareApi::images()->upload($path);

        $this->assertTrue(CloudflareApi::images()->exists($response->result->id));

        CloudflareApi::images()->delete($response->result->id);
    }

    // url() tests

    public function testUrlReturnsString(): void
    {
        $url = CloudflareApi::images()->url(Str::random(10));

        $this->assertIsString($url);
        $this->assertNotEmpty($url);
    }

    public function testUrlWithVariation(): void
    {
        $imageId = Str::random(10);
        $variation = 'thumbnail';

        $url = CloudflareApi::images()->url($imageId, $variation);

        $this->assertStringContainsString($imageId, $url);
        $this->assertStringContainsString($variation, $url);
    }

    /**
     * Upload image and delete it. Allow to assert smth for real image
     *
     * @param  Closure  $closure
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

    private function uploadedImageViaURLAssertions(Closure $closure): void
    {
        $imageUrl = 'https://placehold.co/400';

        $response = CloudflareApi::images()->uploadViaUrl($imageUrl);

        $this->assertInstanceOf(DetailsResponse::class, $response);

        $closure($response);

        $deleteResponse = CloudflareApi::images()->delete($response->result->id);
        $this->assertInstanceOf(DetailsResponse::class, $deleteResponse);
        $this->assertTrue($deleteResponse->success);
    }
}
