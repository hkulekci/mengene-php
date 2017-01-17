# Mengene PHP

Mengene PHP is a PHP library for Mengene API.

## Installation

You can install the library via [Composer](http://getcomposer.org). Run the following command:

```bash
$ composer require mengene-io/mengene-php
```

Then require the `vendor/autoload.php` file to enable auto-loading provided by Composer.

## Usage

### Processing Local Image

To process an image file on your local machine use `setLocalImage()` method. This will upload the file to Mengene API
service.

```php
<?php
use Mengene\Client;

$client = new Client('your-api-key');
$client->setLocalImage('sample.png');
$result = $client->process();
```

### Processing Remote Image

To process an image file from remote location you can use `setRemoteImage()` method. Mengene API service will download
the file from given url to make it ready for processing. The url must be publicly accessible. 

```php
<?php
use Mengene\Client;

$client = new Client('your-api-key');
$client->setRemoteImage('http://example.com/sample.png');
$result = $client->process();
```

## Getting User Status

```php
<?php
use Mengene\Client;

$client = new Client('your-api-key');
$client->status();
```

## Options

### Compression Level

Default compression level is `high` if not specified. Available compression levels are:

* `Client::COMPRESSION_HIGH` slow but the smaller file size
* `Client::COMPRESSION_MEDIUM`
* `Client::COMPRESSION_LOW` fast but the larger file size

```php
<?php
use Mengene\Client;

$client = new Client('your-api-key');
$client
    ->setLocalImage('sample.png')
    ->setCompressionLevel(Client::COMPRESSION_LOW);
$result = $client->process('sample.png');
```

### Optimization Mode

Default optimization mode is `lossy` if not specified. Available optimization modes are:

* `Client::OPTIMIZATION_LOSSY` for smaller file size
* `Client::OPTIMIZATION_LOSSLESS`

```php
<?php
use Mengene\Client;

$client = new Client('your-api-key');
$client
    ->setLocalImage('sample.png')
    ->setOptimizationMode(Client::OPTIMIZATION_LOSSLESS);
$response = $client->process();
```

### Quality

Only available if the image file is `JPEG` and `lossy` optimization mode is used. Default quality level is
automatically calculated by using input image file quality. Quality must be between `1` and `100`.

```php
<?php
use Mengene\Client;

$client = new Client('your-api-key');
$client
    ->setLocalImage('sample.jpg')
    ->setQuality(80);
$response = $client->process();
```

### Chroma Sub-Sampling

Only available if the image file is `JPEG` and `lossy` optimization mode is used. Default sampling scheme is `4:2:0`.
Available sampling schemes are:

* `Client::SAMPLING_SCHEME_444`
* `Client::SAMPLING_SCHEME_422`
* `Client::SAMPLING_SCHEME_420`

For more information about chroma sub-sampling, see
[Wikipedia article](https://en.wikipedia.org/wiki/Chroma_subsampling) about it.

```php
<?php
use Mengene\Client;

$client = new Client('your-api-key');
$client
    ->setLocalImage('sample.jpg')
    ->setSamplingScheme(Client::SAMPLING_SCHEME_444);
$response = $client->process();
```

## About

### Author

Hidayet Doğan - <hdogan@gmail.com> - <https://twitter.com/hdogan>

### License

Mengene PHP is licensed under the MIT license. See the `LICENSE` file for more details.

