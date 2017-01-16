# Mengene PHP

Mengene PHP is a PHP library for Mengene API.

## Installation

You can install the library with [Composer](http://getcomposer.org). Run the following command:

```bash
$ composer require mengene/mengene-php
```

## Basic Usage

```php
<?php
require 'vendor/autoload.php';

use Mengene\Client;

$client = new Client('your-api-key');
$result = $client->upload('sample.png');
```

## Options

### Setting Compression Level

Default compression level is `high`. Available compression levels are:

* `Client::COMPRESSION_HIGH` slow but result will be smaller file size
* `Client::COMPRESSION_MEDIUM`
* `Client::COMPRESSION_LOW` quick but result will be larger file size

```php
use Mengene\Client;

$client = new Client('your-api-key');
$client->setCompressionLevel(Client::COMPRESSION_LOW);
$result = $client->upload('sample.png');
```

### Setting Optimization Mode

Default optimization mode is `lossy`. Available optimization modes are:

* `Client::OPTIMIZATION_LOSSY` for smaller file size
* `Client::OPTIMIZATION_LOSSLESS`

### Setting Quality

Only available if file is `JPEG` and `lossy` optimization mode is used. Default quality
level is automatically calculated with input file quality. Quality must be between `1` and `100`.

```php
use Mengene\Client;

$client = new Client('your-api-key');
$client
    ->setOptimizationMode(Client::OPTIMIZATION_LOSSY);
    ->setQuality(80);
$response = $client->upload('sample.jpg');
```

## About

### Author

Hidayet Doğan - <hdogan@gmail.com> - <https://twitter.com/hdogan>

### License

Mengene PHP is licensed under the MIT license. See the `LICENSE` file for more details.

