<?php

namespace DeDmytro\CloudflareImages\Http\Responses;

use DeDmytro\CloudflareImages\Http\Entities\ArrayableEntity;
use DeDmytro\CloudflareImages\Http\Entities\Image;

class DetailsResponse extends BaseResponse
{
    /**
     * Define array result
     *
     * @var ArrayableEntity|array|Image
     */
    public $result;

    /**
     * DetailsResponse response
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
     * Map array into details result info
     *
     * @param  string  $class
     *
     * @return $this
     */
    final public function mapResultInto(string $class): self
    {
        if (is_subclass_of($class, ArrayableEntity::class)) {
            $this->result = $class::fromArray($this->result);
        }

        return $this;
    }
}
