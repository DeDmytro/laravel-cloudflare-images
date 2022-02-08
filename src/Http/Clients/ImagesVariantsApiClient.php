<?php

namespace DeDmytro\CloudflareImages\Http\Clients;

use DeDmytro\CloudflareImages\Http\Entities\DirectUploadInfo;
use DeDmytro\CloudflareImages\Http\Entities\Image;
use DeDmytro\CloudflareImages\Http\Entities\ImageVariant;
use DeDmytro\CloudflareImages\Http\Responses\DetailsResponse;
use DeDmytro\CloudflareImages\Http\Responses\DirectUploadResponse;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use DeDmytro\CloudflareImages\Exceptions\NoKeyOrAccountProvided;
use Psr\Http\Message\UploadedFileInterface;

class ImagesVariantsApiClient
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

        $this->httpClient = Http::withToken($key)->baseUrl("https://api.cloudflare.com/client/v4/accounts/$account/images/v1");
    }

    /**
     * Return list of images variants
     *
     * @return ListResponse
     */
    public function list(): ListResponse
    {
        return ListResponse::fromArray($this->httpClient->get('variants')->json())->mapResultInto(ImageVariant::class, 'variants');
    }

    /**
     * Return variant by ID
     *
     * @param  string  $variantId
     *
     * @return DetailsResponse
     */
    public function get(string $variantId): DetailsResponse
    {
        return DetailsResponse::fromArray($this->httpClient->get("variants/$variantId")->json())->mapResultInto(ImageVariant::class);
    }

    /**
     * Delete image by ID
     *
     * @param  string  $variantId
     *
     * @return DetailsResponse
     */
    public function delete(string $variantId): DetailsResponse
    {
        return DetailsResponse::fromArray($this->httpClient->delete("variants/$variantId")->json());
    }
}
