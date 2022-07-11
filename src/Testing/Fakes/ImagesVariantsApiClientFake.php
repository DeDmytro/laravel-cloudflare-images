<?php

namespace DeDmytro\CloudflareImages\Testing\Fakes;

use DeDmytro\CloudflareImages\Http\Clients\ImagesVariantsApiClient;
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
use Illuminate\Support\Str;
use Psr\Http\Message\UploadedFileInterface;

class ImagesVariantsApiClientFake extends ImagesVariantsApiClient
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
     * Return list of images variants
     *
     * @return ListResponse
     */
    public function list(): ListResponse
    {
        $result = [
            'result'   => ['variants' => [$this->testVariationData(), $this->testVariationData(), $this->testVariationData()]],
            'success'  => true,
            'errors'   => [],
            'messages' => [],
        ];

        return ListResponse::fromArray($result)->mapResultInto(ImageVariant::class, 'variants');
    }

    /**
     * Return test variation data and save id to static
     *
     * @return array
     */
    private function testVariationData(): array
    {
        $id = Str::random(64);

        static::$createdFakeIds[] = $id;

        return [
            'id'                     => $id,
            'options'                => [],
            'neverRequireSignedURLs' => true,
        ];
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
        $result = $this->testVariationData();

        return DetailsResponse::fromArray($result)->mapResultInto(ImageVariant::class);
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
        $result = [
            'result'   => [],
            'success'  => true,
            'errors'   => [],
            'messages' => [],
        ];

        return DetailsResponse::fromArray($result);
    }
}
