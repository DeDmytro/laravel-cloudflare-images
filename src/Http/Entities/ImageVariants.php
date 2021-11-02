<?php

namespace DeDmytro\CloudflareImages\Http\Entities;

use DeDmytro\CloudflareImages\Http\Responses\BaseResponse;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ImageVariants implements ArrayableEntity
{
    private array $variants = [];

    /**
     * ImageVariants constructor
     *
     * @param  array  $variants
     */
    public function __construct(array $variants)
    {
        foreach ($variants as $variant) {
            $key                  = Str::afterLast($variant, '/');
            $this->variants[$key] = $variant;
        }
    }

    /**
     * Return new instance
     *
     * @param  array  $array
     *
     * @return self
     */
    final public static function fromArray(array $array)
    {
        return new self($array);
    }

    /**
     * @inheritDoc
     */
    final public function toArray(): array
    {
        return $this->variants;
    }

    public function __get(string $name)
    {
        if (array_key_exists($name, $this->variants)) {
            return $this->variants[$name];
        }

        return '';
    }
}
