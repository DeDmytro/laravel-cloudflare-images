<?php

namespace DeDmytro\CloudflareImages\Http\Entities;

use Carbon\Carbon;
use Closure;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Image implements ArrayableEntity
{
    public string $id;

    public string $filename;

    public array $metadata;

    public bool $requireSignedURLs;

    public ImageVariants $variants;

    public ?DateTimeInterface $uploaded;

    /**
     * Image constructor
     *
     * @param  string  $id
     * @param  string  $filename
     * @param  array  $metadata
     * @param  bool  $requireSignedURLs
     * @param  ImageVariants  $variants
     * @param  ?DateTimeInterface  $uploaded
     */
    public function __construct(string $id, string $filename, array $metadata, bool $requireSignedURLs, ImageVariants $variants, $uploaded)
    {
        $this->id                = $id;
        $this->filename          = $filename;
        $this->metadata          = $metadata;
        $this->requireSignedURLs = $requireSignedURLs;
        $this->variants          = $variants;
        $this->uploaded          = Carbon::parse($uploaded);
    }

    /**
     * Return Image instance from array
     *
     * @param  array  $array
     *
     * @return Image
     */
    public static function fromArray(array $array)
    {
        return new self(
            (string) Arr::get($array, 'id'),
            (string) Arr::get($array, 'filename'),
            (array) Arr::get($array, 'metadata'),
            (bool) Arr::get($array, 'requireSignedURLs'),
            ImageVariants::fromArray((array) Arr::get($array, 'variants')),
            Arr::get($array, 'uploaded'),
        );
    }

    /**
     * @inheritDoc
     */
    final public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'filename'          => $this->filename,
            'metadata'          => $this->metadata,
            'requireSignedURLs' => $this->requireSignedURLs,
            'variants'          => $this->variants->toArray(),
            'uploaded'          => $this->uploaded,
        ];
    }
}
