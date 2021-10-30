<?php

namespace DeDmytro\CloudflareImages\Http\Responses;

use DeDmytro\CloudflareImages\Http\Entities\Image;
use Illuminate\Support\Arr;

class ListResponse extends BaseResponse
{
    /**
     * Define array result
     *
     * @var array
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
        parent::__construct(array_map(static fn (array $image) => Image::fromArray($image), Arr::get($result, 'images', [])), $success, $errors, $messages);
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
