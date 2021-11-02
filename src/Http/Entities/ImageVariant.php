<?php

namespace DeDmytro\CloudflareImages\Http\Entities;

use Carbon\Carbon;
use Closure;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ImageVariant implements ArrayableEntity
{
    public string $id;

    public array $options;

    public bool $neverRequireSignedURLs;

    /**
     * ImageVariant constructor
     *
     * @param  string  $id
     * @param  array  $options
     * @param  bool  $neverRequireSignedURLs
     */
    public function __construct(string $id, array $options, bool $neverRequireSignedURLs)
    {
        $this->id                     = $id;
        $this->options                = $options;
        $this->neverRequireSignedURLs = $neverRequireSignedURLs;
    }

    /**
     * Return Photo instance from array
     *
     * @param  array  $array
     *
     * @return ImageVariant
     */
    public static function fromArray(array $array)
    {
        return new self(
            (string) Arr::get($array, 'id'),
            (array) Arr::get($array, 'options'),
            (bool) Arr::get($array, 'neverRequireSignedURLs'),
        );
    }

    /**
     * @inheritDoc
     */
    final public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'metadata'          => $this->options,
            'requireSignedURLs' => $this->neverRequireSignedURLs,
        ];
    }
}
