<?php

namespace DeDmytro\CloudflareImages\Http\Responses;

use DeDmytro\CloudflareImages\Http\Entities\Image;

class DetailsResponse extends BaseResponse
{
    /**
     * Define array result
     *
     * @var Image
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
        parent::__construct(Image::fromArray($result), $success, $errors, $messages);
    }
}
