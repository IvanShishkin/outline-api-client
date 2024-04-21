# OutlineVPN API Client

A simple API client for [OutlineVPN](https://getoutline.org/ru/) written in PHP.

The project implemented a class for working with OutlineVPN api methods and a class for convenient interaction with access keys

## Installation

Install the latest version with

```bash
$ composer require intensa/outline-api-client
```

## Requirements

PHP >= 8.0

## How to use

### Usage API Client

```php

require 'vendor/autoload.php';

use OutlineApiClient\Api\OutlineApiClient;
use OutlineApiClient\Http\Client\CurlClient;
use OutlineApiClient\Exceptions\OutlineException;

try {
    // Your Outline server address
    $serverUrl = 'https://127.0.0.1:3333/YZwl3D1r-B6cNYzQ';
    
    $api = new OutlineApiClient(
        $serverUrl,
        new CurlClient(['timeout' => 10])
    );

    // Get an array of all server keys
    $keysList = $api->getAccessKeys();

    // Create new key
    $key = $api->createNewAccessKey();

    // Rename exist key.
    // Passing key id and new name
    $api->renameAccessKey($key['id'], 'New key name');

    // Set transfer data limit for key.
    // Passing key id and limit in bytes.
    // In the example set 5MB
    $api->setDataLimitForAccessKey($key['id'], 5 * 1024 * 1024);

    // Remove key limit
    // Passing key id
    $api->deleteDataLimitForAccessKey($key['id']);

    // Delete key
    $api->deleteAccessKey($key['id']);

    // Get an array of used traffic for all keys
    $transferData = $api->getMetricsTransfer();
} catch (OutlineException $e) {
    // Handle exception
}
```

### Usage OutlineVPN key wrapper

Interaction with an existing key

```php
<?php
require 'vendor/autoload.php';

use OutlineApiClient\OutlineKey;
use OutlineApiClient\Api\OutlineApiClient;
use OutlineApiClient\Exceptions\OutlineException;
use OutlineApiClient\Http\Client\GuzzleBridgeHttpClient;

try {

    // Your Outline server address
    $serverUrl = 'https://127.0.0.1:3333/YZwl3D1r-B6cNYzQ';
    $outlineApiClient = new GuzzleBridgeHttpClient((new \GuzzleHttp\Client(['verify' => false])));
    // Key id
    $keyId = 1;
    
    // Initializing an object and getting key data
    $key = (new OutlineKey(
            new OutlineApiClient($serverUrl, $outlineApiClient)
        )
    ))->load($keyId);
    
    // Get key id
    $key->getId();
    
    // Get key name
    $key->getName();
    
    // Get key transfer traffic
    $key->getTransfer();
    
    // Get access link 
    $key->getAccessUrl();

    // Rename exist key.
    // Passing key id and new name
    $key->rename('New name');

    // Set transfer data limit for key.
    // Passing limit in bytes.
    // In the example set 5MB
    $key->limit(5 * 1024 * 1024);

    // Remove key traffic limit
    $key->deleteLimit();
    
    // Delete key
    $key->delete();
    
} catch (OutlineException $e) {
    // Handle exception
}

```

Creating a new key on the server

```php
<?php
require 'vendor/autoload.php';

use OutlineApiClient\OutlineKey;
use OutlineApiClient\Api\OutlineApiClient;
use OutlineApiClient\Http\Client\CurlClient;
use OutlineApiClient\Exceptions\OutlineException;

try {
    // Your Outline server address
    $serverUrl = 'https://127.0.0.1:3333/YZwl3D1r-B6cNYzQ';
    
    // Initializing an object and creating new key
    // Passing to method create() key name and traffic limit (optional)
   $key = (new OutlineKey(
        new OutlineApiClient(
            $serverUrl,
            new CurlClient(['timeout' => 10]),
        )
    ))->create('Key name', 5 * 1024 * 1024);

} catch (OutlineException $e) {

}
```