<?php

namespace DeDmytro\CloudflareImages\Http\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Ramsey\Collection\ArrayInterface;

interface ArrayableEntity extends Arrayable
{
    /**
     * Get the instance as an array.
     *
     * @param  array  $data
     *
     * @return \DeDmytro\CloudflareImages\Http\Entities\ArrayableEntity
     */
    public static function fromArray(array $data);
}
