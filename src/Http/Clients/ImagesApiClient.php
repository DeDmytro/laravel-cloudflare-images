<?php

namespace DeDmytro\CloudflareImages\Http\Clients;

use DeDmytro\CloudflareImages\Exceptions\CloudflareImageNotFound;
use DeDmytro\CloudflareImages\Exceptions\NoImageDeliveryUrlProvided;
use DeDmytro\CloudflareImages\Http\Entities\DirectUploadInfo;
use DeDmytro\CloudflareImages\Http\Entities\Image;
use DeDmytro\CloudflareImages\Http\Responses\DetailsResponse;
use DeDmytro\CloudflareImages\Http\Responses\DirectUploadResponse;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use DeDmytro\CloudflareImages\Exceptions\NoKeyOrAccountProvided;
use Psr\Http\Message\UploadedFileInterface;
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
     * @param  string|\Illuminate\Http\UploadedFile  $file
     * @param  bool  $requiredSignedUrl
     * @param  array  $metadata
     *
     * @return DetailsResponse
     */
    public function upload($file, string $filename = '', bool $requiredSignedUrl = false, array $metadata = [],$customId = null): DetailsResponse
    {
        if ($file instanceof UploadedFile) {
            $path = $file->getRealPath();
        } else {
            $path = $file;
        }

        $reqBody = [
                'file' => [
                    'Content-type' => 'multipart/form-data',
                    'name'         => 'file',
                    'contents'     => fopen($path, 'rb'),
                    'filename'     => $filename ?: basename($path),
                ],
                'requireSignedURLs' => var_export($requiredSignedUrl, true),
                'metadata'          => \GuzzleHttp\json_encode($metadata),
            ];
        
        if ($customId) {
            $reqBody['file']['id'] = $customId;
        }
        
        $result = $this->httpClient
            ->asMultipart()
            ->post('v1', $reqBody)->json();

        return DetailsResponse::fromArray($result)->mapResultInto(Image::class);
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
     * @throws \DeDmytro\CloudflareImages\Exceptions\CloudflareImageNotFound
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
    public function directUploadUrl(): DetailsResponse
    {
        return DetailsResponse::fromArray($this->httpClient->post('v1/direct_upload')->json())->mapResultInto(DirectUploadInfo::class);
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
}
