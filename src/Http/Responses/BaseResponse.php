<?php

namespace DeDmytro\CloudflareImages\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

abstract class BaseResponse implements Arrayable
{
    /**
     * @var array|object
     */
    public $result;

    /**
     * @var bool
     */
    public bool $success;

    /**
     * Defines
     * @var array
     */
    public array $errors;

    /**
     * @var array
     */
    public array $messages;

    /**
     * @param  array|object  $result
     * @param  bool  $success
     * @param  array  $errors
     * @param  array  $messages
     */
    public function __construct($result, bool $success = true, array $errors = [], array $messages = [])
    {
        $this->result   = $result;
        $this->success  = $success;
        $this->errors   = $errors;
        $this->messages = $messages;
    }

    /**
     * Return new instance
     *
     * @param  array  $array
     * @return static
     */
    final public static function fromArray(array $array)
    {
        return new static(
            Arr::get($array, 'result'),
            (bool) Arr::get($array, 'success'),
            Arr::get($array, 'errors', []),
            Arr::get($array, 'messages', []),
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'success'  => $this->success,
            'errors'   => $this->errors,
            'messages' => $this->messages,
            'result'   => $this->result,
        ];
    }
}
