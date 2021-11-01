<?php

namespace DeDmytro\CloudflareImages\Http\Responses;

use DeDmytro\CloudflareImages\Http\Entities\DirectUploadInfo;
use DeDmytro\CloudflareImages\Http\Entities\Image;

class DirectUploadResponse extends BaseResponse
{
    /**
     * Define array result
     *
     * @var DirectUploadInfo
     */
    public $result;

    /**
     * DirectUploadResponse response
     *
     * @param  array  $result
     * @param  bool  $success
     * @param  array  $errors
     * @param  array  $messages
     */
    public function __construct(array $result, bool $success = true, array $errors = [], array $messages = [])
    {
        parent::__construct(DirectUploadInfo::fromArray($result), $success, $errors, $messages);
    }
}
