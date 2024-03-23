<?php

namespace DeDmytro\CloudflareImages\Http\Responses;

use DeDmytro\CloudflareImages\Exceptions\CloudflareImagesApiUnexpectedError;
use DeDmytro\CloudflareImages\Exceptions\IncorrectKeyOrAccountProvided;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use TypeError;

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
     *
     * @var array
     */
    public array $errors;

    /**
     * @var array
     */
    public array $messages;

    public const RESPONSE_CODE_MISSING_AUTHORIZATION_KEYS = 9106;

    public const RESPONSE_CODE_AUTHENTICATION_ERROR = 10000;

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
     *
     * @throws IncorrectKeyOrAccountProvided
     * @throws CloudflareImagesApiUnexpectedError
     * @return static
     */
    final public static function fromArray(array $array)
    {
        try {
            return new static(
                Arr::get($array, 'result'),
                (bool) Arr::get($array, 'success'),
                Arr::get($array, 'errors', []),
                Arr::get($array, 'messages', []),
            );
        }
        catch (TypeError $exception) {
            $responseCode = Arr::get($array, 'errors.0.code');

            if (in_array($responseCode, [self::RESPONSE_CODE_AUTHENTICATION_ERROR, self::RESPONSE_CODE_MISSING_AUTHORIZATION_KEYS], true)) {
                throw new IncorrectKeyOrAccountProvided();
            }

            throw new CloudflareImagesApiUnexpectedError(Arr::get($array, 'errors.0.message'));
        }
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
