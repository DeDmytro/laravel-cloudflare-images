<?php

namespace DeDmytro\CloudflareImages\Http\Responses;

use DeDmytro\CloudflareImages\Http\Entities\ArrayableEntity;
use DeDmytro\CloudflareImages\Http\Entities\Image;
use DeDmytro\CloudflareImages\Http\Entities\ImageVariant;
use Illuminate\Support\Arr;

class ListResponse extends BaseResponse
{
    /**
     * Define array result
     *
     * @var array|ArrayableEntity[]|Image[]|ImageVariant[]
     */
    public $result;

    /**
     * List response
     *
     * @param  array  $result
     * @param  bool  $success
     * @param  array  $errors
     * @param  array  $messages
     */
    public function __construct(array $result, bool $success = true, array $errors = [], array $messages = [])
    {
        parent::__construct($result, $success, $errors, $messages);
    }

    /**
     * Map result items into array of objects
     *
     * @param  string  $class
     * @param  string  $key
     *
     * @return $this
     */
    final public function mapResultInto(string $class, string $key): self
    {
        if ($key && is_subclass_of($class, ArrayableEntity::class)) {
            $this->result = array_map(static fn (array $image) => $class::fromArray($image), Arr::get($this->result, $key, []));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    final public function toArray(): array
    {
        return [
            'success'  => $this->success,
            'errors'   => $this->errors,
            'messages' => $this->messages,
            'result'   => array_map(static fn ($image) => $image->toArray(), $this->result),
        ];
    }
}
