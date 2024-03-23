<?php

namespace DeDmytro\CloudflareImages\Helpers;

use DeDmytro\CloudflareImages\Exceptions\CloudflareSignatureTokenNotProvided;

class SignedUrlGenerator
{
    /**
     * The delivery url for image
     *
     * @var string
     */
    private string $imageUrl;

    /**
     * Contains the expiration time for the signed URL
     *
     * @var int
     */
    private int $expiry = 3600; // 1 hour in seconds

    /**
     * The signature token for private images
     *
     * @var string
     */
    private string $signatureToken;

    /**
     * @param  string  $imageUrl
     */
    public function __construct(string $imageUrl)
    {
        if (! config('cloudflare_images.signature_token')) {
            throw new CloudflareSignatureTokenNotProvided();
        }
        $this->signatureToken = config('cloudflare_images.signature_token');

        $this->imageUrl = $imageUrl;
    }

    /**
     * @param  string  $imageUrl
     *
     * @return self
     */
    public static function fromDeliveryUrl(string $imageUrl): self
    {
        return new self($imageUrl);
    }

    /**
     * Set the expiration time for the signed URL
     *
     * @param  int  $seconds
     *
     * @return $this
     */
    final public function setExpiration(int $seconds): self
    {
        $this->expiry = $seconds;

        return $this;
    }

    /**
     * Generate the signed URL
     *
     * @return string
     */
    final public function generate(): string
    {
        $urlParts = parse_url($this->imageUrl);
        $urlPath  = $urlParts['path'];

        // Attach the expiration value to the `url`
        $expiry             = time() + $this->expiry;
        $queryParams['exp'] = $expiry;

        // Generate the string to sign (including query parameters)
        $stringToSign = $urlPath . '?' . http_build_query($queryParams);

        // Generate the signature
        $mac = hash_hmac('sha256', $stringToSign, $this->signatureToken);

        // And attach it to the `url`
        $queryParams['sig'] = $mac;

        // Rebuild the URL with the signature
        return $urlParts['scheme'] . '://' . $urlParts['host'] . $urlPath . '?' . http_build_query($queryParams);
    }
}
