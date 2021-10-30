<?php

namespace DeDmytro\CloudflareImages\Http\Entities;

use DeDmytro\CloudflareImages\Http\Responses\BaseResponse;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class ImageVariants implements Arrayable
{
    public ?string $thumbnail;

    public ?string $hero;

    public ?string $original;

    /**
     * ImageVariants constructor
     *
     * @param  string|null  $thumbnail
     * @param  string|null  $hero
     * @param  string|null  $original
     */
    public function __construct(?string $thumbnail, ?string $hero, ?string $original)
    {
        $this->thumbnail = $thumbnail;
        $this->hero      = $hero;
        $this->original  = $original;
    }

    /**
     * Return new instance
     *
     * @param  array  $array
     *
     * @return self
     */
    final public static function fromArray(array $array): self
    {
        return new self(
            Arr::get($array, 'thumbnail', Arr::get($array, 1)),
            Arr::get($array, 'hero', Arr::get($array, 0)),
            Arr::get($array, 'original', Arr::get($array, 2)),
        );
    }

    /**
     * @inheritDoc
     */
    final public function toArray(): array
    {
        return [
            'thumbnail' => $this->thumbnail,
            'hero'      => $this->hero,
            'original'  => $this->original,
        ];
    }
}
