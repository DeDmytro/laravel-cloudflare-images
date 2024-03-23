<?php

namespace DeDmytro\CloudflareImages\Http\Entities;

use Illuminate\Contracts\Support\Arrayable;

interface ArrayableEntity extends Arrayable
{
    /**
     * Get the instance as an array.
     *
     * @param  array  $data
     *
     * @return ArrayableEntity
     */
    public static function fromArray(array $data);
}
