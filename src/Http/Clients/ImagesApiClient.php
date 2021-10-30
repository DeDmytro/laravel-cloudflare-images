<?php

namespace DeDmytro\CloudflareImages\Http\Clients;

use DeDmytro\CloudflareImages\Http\Responses\DetailsResponse;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use DeDmytro\CloudflareImages\Exceptions\NoKeyOrAccountProvided;
use Psr\Http\Message\UploadedFileInterface;

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
    final public function upload($file, bool $requiredSignedUrl = false, array $metadata = []): DetailsResponse
    {
        if ($file instanceof UploadedFile) {
            $path = $file->getRealPath();
        } else {
            $path = $file;
        }

        $result = $this->httpClient
            ->asMultipart()
            ->post('v1', [
                'file'              => [

                    'Content-type' => 'multipart/form-data',
                    'name'         => 'file',
                    'contents'     => fopen($path, 'rb'),
                    'filename'     => basename($path),

                ],
                'requireSignedURLs' => var_export($requiredSignedUrl, true),
                'metadata'          => \GuzzleHttp\json_encode($metadata),
            ])->json();

        return DetailsResponse::fromArray($result);
    }

    /**
     * Return list of images
     *
     * @param  int  $page
     * @param  int  $perPage
     *
     * @return ListResponse
     */
    final public function list(int $page = 1, int $perPage = 50): ListResponse
    {
        return ListResponse::fromArray($this->httpClient->get('v1', ['page' => $page, 'per_page' => $perPage])->json());
    }

    /**
     * Return image details by ID
     *
     * @param  string  $imageId
     *
     * @return DetailsResponse
     */
    final public function get(string $imageId): DetailsResponse
    {
        return DetailsResponse::fromArray($this->httpClient->get("v1/$imageId")->json());
    }

    /**
     * Return image details by ID
     *
     * @param  string  $imageId
     *
     * @return DetailsResponse
     */
    final public function delete(string $imageId): DetailsResponse
    {
        return DetailsResponse::fromArray($this->httpClient->delete("v1/$imageId")->json());
    }
}
