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
use Mengene\ImageOptions;

$client = new Client('your-api-key');
$options = (new ImageOptions())
    ->setFile('sample.jpg');
$result = $client->process($options);
```

Result array will contain information about optimization process and also download url of the optimized image.

```json
{
    "success": true,
    "source_name": "sample.png",
    "source_size": 5667,
    "options": {
        "compression_level": "high",
        "optimization_mode": "lossy",
        "quality": null,
        "sampling_scheme": null
    },
    "output_size": 2086,
    "output_saving": 3581,
    "output_ratio": 0.36809599435327,
    "output_percentage": 63.190400564673,
    "output_url": "https://download.mengene.io/03c3d602d5643ed484e282ee76910cce.png"
}
```

You can use `$result['output_url']` to download optimized image manually. There is also `download()` helper
method to make it downloading easy. Also note that, optimized images are available for download for only 1 hour.

### Processing Remote Image

To process an image file from remote location you can use `setRemoteImage()` method. Mengene API service will download
the file from given url to make it ready for processing. The url must be publicly accessible. 

```php
<?php
use Mengene\Client;
use Mengene\ImageOptions;

$client = new Client('your-api-key');
$options = (new ImageOptions())
    ->setUrl('http://example.com/sample.png');
$result = $client->process($options);
```

### Download Processed Image

`$result['output_url']` gives download url of the optimized image. PHP library provides
simple `download()` helper method. Note that, optimized images are available for download for only 1 hour.

```php
use Mengene\Client;
use Mengene\ImageOptions;

$client = new Client('your-api-key');
$options = (new ImageOptions())
    ->setFile('sample.jpg');
$result = $client->process($options);
$client->download($result, 'path/to/processed-image.png');
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

* `ImageOptions::COMPRESSION_HIGH` slow but the smaller file size
* `ImageOptions::COMPRESSION_MEDIUM`
* `ImageOptions::COMPRESSION_LOW` fast but the larger file size

```php
<?php
use Mengene\Client;
use Mengene\ImageOptions;

$client = new Client('your-api-key');
$options = (new ImageOptions())
    ->setFile('sample.jpg')
    ->setCompressionLevel(ImageOptions::COMPRESSION_LOW);
$result = $client->process($options);
```

### Optimization Mode

Default optimization mode is `lossy` if not specified. Available optimization modes are:

* `ImageOptions::OPTIMIZATION_LOSSY` for smaller file size
* `ImageOptions::OPTIMIZATION_LOSSLESS`

```php
<?php
use Mengene\Client;
use Mengene\ImageOptions;

$client = new Client('your-api-key');
$options = (new ImageOptions())
    ->setFile('sample.jpg')
    ->setOptimizationMode(ImageOptions::OPTIMIZATION_LOSSLESS);
$response = $client->process($options);
```

### Quality

Only available if the image file is `JPEG` and `lossy` optimization mode is used. Default quality level is
automatically calculated by using input image file quality. Quality must be between `1` and `100`.

```php
<?php
use Mengene\Client;
use Mengene\ImageOptions;

$client = new Client('your-api-key');
$options = (new ImageOptions())
    ->setFile('sample.jpg')
    ->setQuality(80);
$response = $client->process($options);
```

### Chroma Sub-Sampling

Only available if the image file is `JPEG` and `lossy` optimization mode is used. Default sampling scheme is `4:2:0`.
Available sampling schemes are:

* `ImageOptions::SAMPLING_SCHEME_444`
* `ImageOptions::SAMPLING_SCHEME_422`
* `ImageOptions::SAMPLING_SCHEME_420`

For more information about chroma sub-sampling, see
[Wikipedia article](https://en.wikipedia.org/wiki/Chroma_subsampling) about it.

```php
<?php
use Mengene\Client;
use Mengene\ImageOptions;

$client = new Client('your-api-key');
$options = (new ImageOptions())
    ->setFile('sample.jpg')
    ->setSamplingScheme(ImageOptions::SAMPLING_SCHEME_444);
$response = $client->process($options);
```

### Author

* Hidayet Doğan - [@hdogan](https://twitter.com/hdogan) - http://hi.do
* Haydar Külekci - [@kulekci](https://twitter.com/kulekci) - http://kulekci.net

### License

Mengene PHP is licensed under the MIT license. See the `LICENSE` file for more details.

