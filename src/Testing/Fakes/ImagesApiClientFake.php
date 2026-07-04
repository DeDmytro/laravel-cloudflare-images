<?php

namespace DeDmytro\CloudflareImages\Testing\Fakes;

use DeDmytro\CloudflareImages\Exceptions\CloudflareSignedUrlNotSupportedForCustomIds;
use DeDmytro\CloudflareImages\Exceptions\NoKeyOrAccountProvided;
use DeDmytro\CloudflareImages\Helpers\SignedUrlGenerator;
use DeDmytro\CloudflareImages\Http\Clients\ImagesApiClient;
use DeDmytro\CloudflareImages\Http\Entities\DirectUploadInfo;
use DeDmytro\CloudflareImages\Http\Entities\Image;
use DeDmytro\CloudflareImages\Http\Responses\DetailsResponse;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
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
        config()->set('cloudflare_images.default_variation', 'public');
        config()->set('cloudflare_images.delivery_url', 'https://imagedelivery.example.net');

        parent::__construct();
    }

    /**
     * Upload file and return details
     *
     * @param  string|UploadedFile  $file
     * @param  string  $filename
     * @param  bool  $requiredSignedUrl
     * @param  array  $metadata
     * @param  string|null  $customId
     *
     * @throws CloudflareSignedUrlNotSupportedForCustomIds
     * @return DetailsResponse
     */
    public function upload($file, string $filename = '', bool $requiredSignedUrl = false, array $metadata = [], string $customId = null): DetailsResponse
    {
        if ($customId && $requiredSignedUrl) {
            throw new CloudflareSignedUrlNotSupportedForCustomIds();
        }

        $imageFilename = $filename ?: ($file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file));
        $data = $this->testImageData($imageFilename, $requiredSignedUrl, $metadata);

        if ($customId) {
            $data['id'] = $customId;
            static::$createdFakeIds[array_key_last(static::$createdFakeIds)] = $customId;
        }

        $result = [
            'result'   => $data,
            'success'  => true,
            'errors'   => [],
            'messages' => [],
        ];

        return DetailsResponse::fromArray($result)->mapResultInto(Image::class);
    }

    /**
     * Upload an image via URL
     *
     * @param  string  $url
     * @param  array  $metadata
     * @param  bool  $requireSignedUrl
     *
     * @return DetailsResponse
     */
    public function uploadViaUrl(string $url, array $metadata = [], bool $requireSignedUrl = false): DetailsResponse
    {
        $data = $this->testImageData(basename($url), $requireSignedUrl, $metadata);

        $result = [
            'result'   => $data,
            'success'  => true,
            'errors'   => [],
            'messages' => [],
        ];

        return DetailsResponse::fromArray($result)->mapResultInto(Image::class);
    }

    /**
     * Clone an image
     *
     * @param  string  $imageId
     * @param  string|null  $customId
     * @param  array  $metadata
     * @param  bool  $requireSignedUrl
     *
     * @throws CloudflareSignedUrlNotSupportedForCustomIds
     * @return DetailsResponse
     */
    public function clone(string $imageId, string $customId = null, array $metadata = [], bool $requireSignedUrl = false): DetailsResponse
    {
        if ($customId && $requireSignedUrl) {
            throw new CloudflareSignedUrlNotSupportedForCustomIds();
        }

        $data = $this->testImageData($imageId, $requireSignedUrl, $metadata);

        if ($customId) {
            $data['id'] = $customId;
            static::$createdFakeIds[array_key_last(static::$createdFakeIds)] = $customId;
        }

        $result = [
            'result'   => $data,
            'success'  => true,
            'errors'   => [],
            'messages' => [],
        ];

        return DetailsResponse::fromArray($result)->mapResultInto(Image::class);
    }

    /**
     * Return test image data and save id to static array
     *
     * @param  string|null  $filename
     * @param  bool  $requireSignedURLs
     * @param  array  $metadata
     *
     * @return array
     */
    private function testImageData(string $filename = null, bool $requireSignedURLs = false, array $metadata = []): array
    {
        $id = Str::random(64);

        static::$createdFakeIds[] = $id;

        return [
            'id'                => $id,
            'filename'          => $filename ?? Str::random(64),
            'metadata'          => $metadata,
            'requireSignedURLs' => $requireSignedURLs,
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
            'result'   => [
                'images' => array_map(function ($item) {
                    return $this->testImageData();
                }, range(1, $perPage)),
            ],
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
     *
     * @param  array  $metadata
     * @param  bool  $requireSignedUrl
     *
     * @return DetailsResponse
     */
    public function directUploadUrl(array $metadata = [], bool $requireSignedUrl = false): DetailsResponse
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
        $variation = $variation ?? config('cloudflare_images.default_variation', 'public');
        $deliveryUrl = config('cloudflare_images.delivery_url', 'https://imagedelivery.example.net');

        return rtrim($deliveryUrl, '/') . '/' . $imageId . '/' . $variation;
    }

    /**
     * Return signed image for private url by image id and variation
     *
     * @param  string  $imageId
     * @param  string|null  $variation
     * @param  int  $expirationSeconds
     *
     * @throws \Throwable
     * @return string
     */
    public function signedUrl(string $imageId, ?string $variation = null, int $expirationSeconds = 3600): string
    {
        return SignedUrlGenerator::fromDeliveryUrl($this->url($imageId, $variation))->setExpiration($expirationSeconds)->generate();
    }
}
