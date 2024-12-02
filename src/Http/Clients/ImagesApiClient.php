<?php

namespace DeDmytro\CloudflareImages\Http\Clients;

use DeDmytro\CloudflareImages\Exceptions\CloudflareImageNotFound;
use DeDmytro\CloudflareImages\Exceptions\CloudflareSignedUrlNotSupportedForCustomIds;
use DeDmytro\CloudflareImages\Exceptions\NoImageDeliveryUrlProvided;
use DeDmytro\CloudflareImages\Exceptions\NoKeyOrAccountProvided;
use DeDmytro\CloudflareImages\Helpers\SignedUrlGenerator;
use DeDmytro\CloudflareImages\Http\Entities\DirectUploadInfo;
use DeDmytro\CloudflareImages\Http\Entities\Image;
use DeDmytro\CloudflareImages\Http\Responses\DetailsResponse;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Throwable;

class ImagesApiClient
{
    /**
     * Contains http client
     *
     * @var PendingRequest
     */
    private PendingRequest $httpClient;

    /**
     * ApiClient constructor
     *
     * @throws NoKeyOrAccountProvided|\Throwable
     */
    public function __construct()
    {
        $account = config('cloudflare_images.account');
        $key     = config('cloudflare_images.key');

        throw_unless($account || $key, new NoKeyOrAccountProvided());

        $this->httpClient = Http::withToken($key)->baseUrl("https://api.cloudflare.com/client/v4/accounts/$account/images");
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
        if ($file instanceof UploadedFile) {
            $path = $file->getRealPath();
        } else {
            $path = $file;
        }

        $body = [
            'file'              => [
                'Content-type' => 'multipart/form-data',
                'name'         => 'file',
                'contents'     => fopen($path, 'rb'),
                'filename'     => $filename ?: basename($path),
            ],
            'requireSignedURLs' => var_export($requiredSignedUrl, true),
            'metadata'          => \GuzzleHttp\json_encode($metadata),
        ];

        if ($customId) {
            if ($requiredSignedUrl) {
                throw new CloudflareSignedUrlNotSupportedForCustomIds();
            }
            $body['id'] = $customId;
        }

        $result = $this->httpClient->asMultipart()->post('v1', $body)->json();

        return DetailsResponse::fromArray($result)->mapResultInto(Image::class);
    }

    /**
     * Upload an image via URL
     *
     * @param  string  $url  The URL of the image to upload
     * @param  array  $metadata  Metadata to include with the upload
     * @param  bool  $requireSignedUrl  Whether signed URLs are required
     *
     * @return DetailsResponse
     * @throws \Throwable
     */
    public function uploadViaUrl(string $url, array $metadata = [], bool $requireSignedUrl = false): DetailsResponse
    {
        $body = [
            'url' => $url,
            'requireSignedURLs' => var_export($requireSignedUrl, true),
            'metadata' => \GuzzleHttp\json_encode($metadata),
        ];

        $result = $this->httpClient->asMultipart()->post('v1', $body)->json();

        return DetailsResponse::fromArray($result)->mapResultInto(Image::class);
    }

    /**
     * Clone an image by downloading it from Cloudflare and uploading it to Cloudflare again.
     *
     * @param  string  $imageId  The ID of the image to clone.
     * @param  string|null  $customId  Optionally provide a custom ID for the new image.
     * @param  array  $metadata  Optional metadata for the new image.
     * @param  bool  $requireSignedUrl  Whether the image requires signed URLs.
     *
     * @throws \DeDmytro\CloudflareImages\Exceptions\CloudflareSignedUrlNotSupportedForCustomIds
     * @throws \DeDmytro\CloudflareImages\Exceptions\CloudflareImageNotFound|\Illuminate\Http\Client\ConnectionException
     * @return DetailsResponse The response with details of the cloned image.
     */
    public function clone(string $imageId, string $customId = null, array $metadata = [], bool $requireSignedUrl = false): DetailsResponse
    {
        $response = $this->httpClient->get("v1/{$imageId}/blob");

        if ($response->failed()) {
            throw new CloudflareImageNotFound($imageId);
        }

        $tmpDir = sys_get_temp_dir(); // System temporary directory
        $tmpFilePath = $tmpDir . DIRECTORY_SEPARATOR . "{$imageId}.tmp";

        file_put_contents($tmpFilePath, $response->body());

        try {
            $detailsResponse = $this->upload($tmpFilePath, $imageId, $requireSignedUrl, $metadata, $customId);
        } finally {
            if (file_exists($tmpFilePath)) {
                unlink($tmpFilePath);
            }
        }

        return $detailsResponse;
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
        return ListResponse::fromArray($this->httpClient->get('v1', ['page' => $page, 'per_page' => $perPage])->json())->mapResultInto(Image::class, 'images');
    }

    /**
     * Return image details by ID
     *
     * @param  string  $imageId
     *
     * @throws CloudflareImageNotFound
     * @throws \Throwable
     * @return DetailsResponse
     */
    public function get(string $imageId): DetailsResponse
    {
        $result = $this->httpClient->get("v1/$imageId")->json();

        throw_if(is_null($result), new CloudflareImageNotFound($imageId));

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
        $result = $this->httpClient->delete("v1/$imageId")->json();

        throw_if(is_null($result), new CloudflareImageNotFound($imageId));

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
        $result = $this->httpClient->get("v1/$imageId")->json();

        if (is_null($result)) {
            return false;
        }

        return DetailsResponse::fromArray($result)->success;
    }

    /**
     * Return direct upload information
     * Direct upload allows uploading files from frontend without sharing the application api key
     *
     * @link https://developers.cloudflare.com/images/cloudflare-images/upload-images/direct-creator-upload
     * @return DetailsResponse
     */
    public function directUploadUrl(array $metadata = [], bool $requireSignedUrl = false): DetailsResponse
    {
        $body = [
            'requireSignedURLs' => var_export($requireSignedUrl, true),
            'metadata' => json_encode($metadata),
        ];

        $result = $this->httpClient->asMultipart()->post('v2/direct_upload', $body)->json();

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
        $imageDeliveryUrl = config('cloudflare_images.delivery_url');

        throw_if(empty($imageDeliveryUrl), new NoImageDeliveryUrlProvided());

        if (! $variation) {
            $variation = config('cloudflare_images.default_variation');
        }

        return ltrim(rtrim($imageDeliveryUrl, '/') . '/' . ltrim("$imageId/$variation", '/'), '/');
    }

    /**
     * Return signed url by image id and variation
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
        return SignedUrlGenerator::fromDeliveryUrl($this->url($imageId,$variation))->setExpiration($expirationSeconds)->generate();
    }
}
