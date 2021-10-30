# Laravel CloudflareImages

Provides access to Cloudflare Images API for Laravel projects

[![Stable Version][badge_stable]][link_packagist]
[![Unstable Version][badge_unstable]][link_packagist]
[![Total Downloads][badge_downloads]][link_packagist]
[![License][badge_license]][link_license]

## Table of contents

* [Installation](#installation)
* [Configuration](#configuration)
* [Using](#using)

## Installation

To get the latest version of `Laravel CloudflareImages`, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require dedmytro/laravel-cloudflare-images
```

Or manually update `require` block of `composer.json` and run `composer update`.

```json
{
    "require": {
        "dedmytro/laravel-cloudflare-images": "^0.1"
    }
}
```

## Configuration

Add two environment variables to your .env file:

```dotenv
CLOUDFLARE_IMAGES_ACCOUNT='your-account-id'
CLOUDFLARE_IMAGES_KEY='your-api-key'
```

or publish config and set up vars there

```php
return [
    'account'=> env('CLOUDFLARE_IMAGES_ACCOUNT'),
    'key'=> env('CLOUDFLARE_IMAGES_KEY'),
];
```

## Using

### Upload

Call `upload()` method and pass file as local file path or `UploadedFile` instance.
As a result of upload you'll get `DetailsResponse` instance with uploaded image details, so you can save it locally.

```php
use DeDmytro\CloudflareImages\Facades\CloudflareImages;
use DeDmytro\CloudflareImages\Http\Responses\DetailsResponse;
use DeDmytro\CloudflareImages\Http\Entities\Image;

/* @var $file \Illuminate\Http\UploadedFile|string */

/* @var $response DetailsResponse*/
$response = CloudflareImages::api()->upload($file)

/* @var $image Image*/
$image = $response->result

$image->id;
$image->filename;
$image->variants->thumbnail;
$image->variants->original;


```

### List

To list existing images you should use `list()` method which also has pagination and accept `$page` and `$perPage` arguments.

```php
use DeDmytro\CloudflareImages\Facades\CloudflareImages;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use DeDmytro\CloudflareImages\Http\Entities\Image;

/* @var $response ListResponse*/
$response = CloudflareImages::api()->list()
//OR
$response = CloudflareImages::api()->list($page, $perPage)

foreach($response->result as $image){
    $image->id;
    $image->filename;
    $image->variants->thumbnail;
    $image->variants->original;
}

```

### Details

To get existing image details you should use `get($id)` method where `$id` is image identifier you received when you list or upload the image.

```php
use DeDmytro\CloudflareImages\Facades\CloudflareImages;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use DeDmytro\CloudflareImages\Http\Entities\Image;

$response = CloudflareImages::api()->get($id)

$image = $response->result;
$image->id;
$image->filename;
$image->variants->thumbnail;
$image->variants->original;


```

### Delete

To delete existing image you should use `delete($id)` method where `$id` is image identifier you received when you list or upload the image.

```php
use DeDmytro\CloudflareImages\Facades\CloudflareImages;
use DeDmytro\CloudflareImages\Http\Responses\ListResponse;
use DeDmytro\CloudflareImages\Http\Entities\Image;

$response = CloudflareImages::api()->delete($id)
$response->success

```

[badge_downloads]:      https://img.shields.io/packagist/dt/dedmytro/laravel-cloudflare-images.svg?style=flat-square

[badge_license]:        https://img.shields.io/packagist/l/dedmytro/laravel-cloudflare-images.svg?style=flat-square

[badge_stable]:         https://img.shields.io/github/v/release/dedmytro/laravel-cloudflare-images?label=stable&style=flat-square

[badge_unstable]:       https://img.shields.io/badge/unstable-dev--main-orange?style=flat-square

[link_license]:         LICENSE

[link_packagist]:       https://packagist.org/packages/dedmytro/laravel-cloudflare-images
