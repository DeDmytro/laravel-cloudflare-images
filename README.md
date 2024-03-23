# Cloudflare Images

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

Add environment variables to your .env file:

```dotenv
CLOUDFLARE_IMAGES_ACCOUNT='your-account-id'
CLOUDFLARE_IMAGES_KEY='your-api-key'
CLOUDFLARE_IMAGES_DELIVERY_URL='https://imagedelivery.net/ZWd9g1K8vvvVv_Yyyy_XXX'
CLOUDFLARE_IMAGES_DEFAULT_VARIATION='your-default-variation'
CLOUDFLARE_IMAGES_SIGNATURE_TOKEN='your-signature-token'
```

or publish config and set up vars there

```php
return [
    'account'=> env('CLOUDFLARE_IMAGES_ACCOUNT'),
    'key'=> env('CLOUDFLARE_IMAGES_KEY'),
    'delivery_url'      => env('CLOUDFLARE_IMAGES_DELIVERY_URL'),
    'default_variation' => env('CLOUDFLARE_IMAGES_DEFAULT_VARIATION'),
    'signature_token'   => env('CLOUDFLARE_IMAGES_SIGNATURE_TOKEN')
];
```

`CLOUDFLARE_IMAGES_KEY` - is an `API Token`. To create a new one go to [User Api Tokens](https://dash.cloudflare.com/profile/api-tokens) on Cloudflare dashboard

`CLOUDFLARE_IMAGES_ACCOUNT` - is an `Account ID` on the Overview page

`CLOUDFLARE_IMAGES_DELIVERY_URL` - is an `Image Delivery URL` on the Overview page

`CLOUDFLARE_IMAGES_DEFAULT_VARIATION` - is a variation on the Variants page

`CLOUDFLARE_IMAGES_SIGNATURE_TOKEN` - is a token from the Images -> Keys page


## Using

### Direct upload

The Direct upload is feature of Cloudflare Images to upload image directly from frontend but without sharing your api key. Once you get this url you can use
inside your html

`<form  method="post" enctype="multipart/form-data"  action="{{ $uploadUrl }}">`

**IMPORTANT: You can use this url only once!**

```php
use DeDmytro\CloudflareImages\Facades\CloudflareApi;

$response = CloudflareApi::images()->directUploadUrl()
$response->result->id; // Your uploaded image ID
$response->result->uploadURL; // One-time uploadUrl

```

### Upload

Call `upload()` method and pass file as local file path or `UploadedFile` instance. As a result of upload you'll get `DetailsResponse` instance with uploaded
image details, so you can save it locally.

```php
use DeDmytro\CloudflareImages\Facades\CloudflareApi;
use DeDmytro\CloudflareImages\Http\Responses\DetailsResponse;
use DeDmytro\CloudflareImages\Http\Entities\Image;

/* @var $file \Illuminate\Http\UploadedFile|string */

/* @var $response DetailsResponse*/
$response = CloudflareApi::images()->upload($file)

/* @var $image Image*/
$image = $response->result

$image->id;
$image->filename;
$image->variants->thumbnail; //Depends on your Cloudflare Images Variants setting
$image->variants->original; //Depends on your Cloudflare Images Variants setting


```

### List

To list existing images you should use `list()` method which also has pagination and accept `$page` and `$perPage` arguments.

```php
use DeDmytro\CloudflareImages\Facades\CloudflareApi;

/* @var $response ListResponse*/
$response = CloudflareApi::images()->list()
//OR
$response = CloudflareApi::images()->list($page, $perPage)

foreach($response->result as $image){
    $image->id;
    $image->filename;
    $image->variants->thumbnail; //Depends on your Cloudflare Images Variants setting
    $image->variants->original; //Depends on your Cloudflare Images Variants setting
}

```

### Details

To get existing image details you should use `get($id)` method where `$id` is image identifier you received when you list or upload the image.

```php
use DeDmytro\CloudflareImages\Facades\CloudflareApi;

$response = CloudflareApi::images()->get($id)

$image = $response->result;
$image->id;
$image->filename;
$image->variants->thumbnail; //Depends on your Cloudflare Images Variants setting
$image->variants->original; //Depends on your Cloudflare Images Variants setting


```

### Delete

To delete existing image you should use `delete($id)` method where `$id` is image identifier you received when you list or upload the image.

```php
use DeDmytro\CloudflareImages\Facades\CloudflareApi;

$response = CloudflareApi::images()->delete($id)
$response->success

```

### Public url

To generate image url locally call method `url($id)` and pass image ID. Don't forget to set up

```dotenv
CLOUDFLARE_IMAGES_DELIVERY_URL=
CLOUDFLARE_IMAGES_DEFAULT_VARIATION=
```

```php
use DeDmytro\CloudflareImages\Facades\CloudflareApi;

$url = CloudflareApi::images()->url($id)
```

```html
<img src="{{ $url }}"/>
```

### Signed url

To generate signed image url locally call method `signedUrl($id, $expires = 3600)` and pass image ID and expiration time in seconds. Don't forget to set up

```dotenv
CLOUDFLARE_IMAGES_DELIVERY_URL=
CLOUDFLARE_IMAGES_DEFAULT_VARIATION=
CLOUDFLARE_IMAGES_SIGNATURE_TOKEN=
```

```php
use DeDmytro\CloudflareImages\Facades\CloudflareApi;

$url = CloudflareApi::images()->signedUrl($id, $expires)
```

```html

<img src="{{ $url }}"/>
```


[badge_downloads]:      https://img.shields.io/packagist/dt/dedmytro/laravel-cloudflare-images.svg?style=flat-square

[badge_license]:        https://img.shields.io/packagist/l/dedmytro/laravel-cloudflare-images.svg?style=flat-square

[badge_stable]:         https://img.shields.io/github/v/release/dedmytro/laravel-cloudflare-images?label=stable&style=flat-square

[badge_unstable]:       https://img.shields.io/badge/unstable-dev--main-orange?style=flat-square

[link_license]:         LICENSE

[link_packagist]:       https://packagist.org/packages/dedmytro/laravel-cloudflare-images
