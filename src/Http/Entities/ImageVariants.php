<?php

namespace DeDmytro\CloudflareImages\Http\Entities;

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

    /**
     * Return image variant url by name
     *
     * @param  string  $name
     *
     * @return mixed|string
     */
    public function __get(string $name)
    {
        if (array_key_exists($name, $this->variants)) {
            return $this->variants[$name];
        }

        return '';
    }

    /**
     * Set/Update variation url by name
     *
     * @param  string  $name
     * @param $value
     */
    public function __set(string $name, $value)
    {
        $this->variants[$name] = $value;
    }

    /**
     * Check variation exists when use isset() or empty() on property
     *
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->variants);
    }
}
