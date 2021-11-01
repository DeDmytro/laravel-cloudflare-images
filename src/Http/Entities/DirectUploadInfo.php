<?php

namespace DeDmytro\CloudflareImages\Http\Entities;

use Carbon\Carbon;
use Closure;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DirectUploadInfo implements Arrayable
{
    public string $id;

    public string $uploadURL;

    /**
     * DirectUploadInfo constructor
     *
     * @param  string  $id
     * @param  string  $uploadURL
     */
    public function __construct(string $id, string $uploadURL)
    {
        $this->id        = $id;
        $this->uploadURL = $uploadURL;
    }

    /**
     * Return Photo instance from array
     *
     * @param  array  $array
     *
     * @return self
     */
    public static function fromArray(array $array): self
    {
        return new self(
            (string) Arr::get($array, 'id'),
            (string) Arr::get($array, 'uploadURL'),
        );
    }

    /**
     * @inheritDoc
     */
    final public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'uploadURL' => $this->uploadURL,
        ];
    }
}
