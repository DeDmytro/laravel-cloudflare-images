<?php

namespace DeDmytro\CloudflareImages\Testing\Fakes;

use DeDmytro\CloudflareImages\Exceptions\CloudflareImageNotFound;
use DeDmytro\CloudflareImages\Exceptions\NoImageDeliveryUrlProvided;
use DeDmytro\CloudflareImages\Http\Clients\ImagesApiClient;
use DeDmytro\CloudflareImages\Http\Entities\DirectUploadInfo;
use DeDmytro\CloudflareImages\Http\Entities\Image;
use DeDmytro\CloudflareImages\Http\Entities\ImageVariants;
use DeDmytro\CloudflareImages\Http\Responses\DetailsResponse;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use DeDmytro\CloudflareImages\Exceptions\NoKeyOrAccountProvided;
use Illuminate\Support\Str;
use Psr\Http\Message\UploadedFileInterface;
use Throwable;

class ImagesApiClientFake extends ImagesApiClient
{
    /**
     * @var array
     */
    private static $createdFakeIds = [];

    /**
     * ApiClient constructor
     *
     * @throws NoKeyOrAccountProvided|\Throwable
     */
    public function __construct()
    {
        config()->set('cloudflare_images.account', 'fake');
        config()->set('cloudflare_images.key', 'fake');
        config()->set('cloudflare_images.default_variation', 'fake');
        config()->set('cloudflare_images.delivery_url', 'fake');

        parent::__construct();
    }

    /**
     * Upload file and return details
     *
     * @param  string|\Illuminate\Http\UploadedFile  $file
     * @param  bool  $requiredSignedUrl
     * @param  array  $metadata
     *
     * @return DetailsResponse
     */
    public function upload($file, string $filename = '', bool $requiredSignedUrl = false, array $metadata = []): DetailsResponse
    {
        $result = [
            'result'   => $this->testImageData(Str::afterLast($file, '/')),
            'success'  => true,
            'errors'   => [],
            'messages' => [],
        ];

        return DetailsResponse::fromArray($result)->mapResultInto(Image::class);
    }

    /**
     * Return test image data and save id to static
     *
     * @param  string|null  $filename
     *
     * @return array
     */
    private function testImageData(string $filename = null): array
    {
        $id = Str::random(64);

        static::$createdFakeIds[] = $id;

        return [
            'id'                => $id,
            'filename'          => $filename ?? Str::random(64),
            'metadata'          => [],
            'requireSignedURLs' => false,
            'variants'          => [],
            'uploaded'          => now()->toDateTimeString(),
        ];
    }

    /**
     * Return list of images
     *
     * @param  int  $page
     * @param  int  $perPage
     *
     * @return ListResponse
     */
    public function list(int $page = 1, int $perPage = 50): ListResponse
    {
        $result = [
            'result'   => array_map(function ($item) {
                return $this->testImageData();
            }, range(1, $perPage)),
            'success'  => true,
            'errors'   => [],
            'messages' => [],
        ];

        return ListResponse::fromArray($result)->mapResultInto(Image::class, 'images');
    }

    /**
     * Return image details by ID
     *
     * @param  string  $imageId
     *
     * @throws \Throwable
     * @return DetailsResponse
     */
    public function get(string $imageId): DetailsResponse
    {
        $result = [
            'result'   => $this->testImageData(),
            'success'  => true,
            'errors'   => [],
            'messages' => [],
        ];

        return DetailsResponse::fromArray($result)->mapResultInto(Image::class);
    }

    /**
     * Delete image by ID
     *
     * @param  string  $imageId
     *
     * @throws \Throwable
     * @return DetailsResponse
     */
    public function delete(string $imageId): DetailsResponse
    {
        $result = [
            'result'   => [],
            'success'  => true,
            'errors'   => [],
            'messages' => [],
        ];

        return DetailsResponse::fromArray($result);
    }

    /**
     * Check image exists by ID
     *
     * @param  string  $imageId
     *
     * @throws \Throwable
     * @return bool
     */
    public function exists(string $imageId): bool
    {
        return in_array($imageId, static::$createdFakeIds, true);
    }

    /**
     * Return direct upload information
     * Direct upload allows uploading files from frontend without sharing the application api key
     *
     * @link https://developers.cloudflare.com/images/cloudflare-images/upload-images/direct-creator-upload
     * @return DetailsResponse
     */
    public function directUploadUrl(): DetailsResponse
    {
        $result = [
            'result'   => [
                'id'        => Str::random(),
                'uploadURL' => 'https://api.cloudflarefake.com/',
            ],
            'success'  => true,
            'errors'   => [],
            'messages' => [],
        ];

        return DetailsResponse::fromArray($result)->mapResultInto(DirectUploadInfo::class);
    }

    /**
     * Return image public url by image id and variation
     *
     * @param  string  $imageId
     * @param  string|null  $variation
     *
     * @throws \Throwable
     * @return string
     */
    public function url(string $imageId, ?string $variation = null): string
    {
        return Str::random(64);
    }
}
