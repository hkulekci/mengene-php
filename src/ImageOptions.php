<?php
/**
 * PHP library for Mengene API
 *
 * @copyright Copyright (c) 2017 Hidayet Doğan
 * @license https://opensource.org/licenses/MIT MIT License
 */
namespace Mengene;

class ImageOptions
{
    /**
     * Compression levels
     */
    const COMPRESSION_HIGH   = 'high';
    const COMPRESSION_MEDIUM = 'medium';
    const COMPRESSION_LOW    = 'low';
    
    /**
     * Optimization modes
     */
    const OPTIMIZATION_LOSSY = 'lossy';
    const OPTIMIZATION_LOSSLESS = 'lossless';

    /**
     * Chroma sub-sampling schemes
     */
    const SAMPLING_SCHEME_444 = '4:4:4';
    const SAMPLING_SCHEME_422 = '4:2:2';
    const SAMPLING_SCHEME_420 = '4:2:0';

    /**
     * @var string $compressionLevel
     */
    protected $compressionLevel;

    /**
     * @var string $file
     */
    protected $file;

    /**
     * @var string $url
     */
    protected $url;

    /**
     * @var string $optimizationMode
     */
    protected $optimizationMode;

    /**
     * @var string $quality
     */
    protected $quality;

    /**
     * @var string $samplingSchema
     */
    protected $samplingSchema;

    /**
     * @return string
     */
    public function getCompressionLevel()
    {
        return $this->compressionLevel;
    }

    /**
     * @param string $compressionLevel
     *
     * @return $this
     * @throws \LogicException
     */
    public function setCompressionLevel($compressionLevel)
    {
        if (in_array($compressionLevel, [self::COMPRESSION_HIGH, self::COMPRESSION_MEDIUM, self::COMPRESSION_LOW])) {
            $this->compressionLevel = $compressionLevel;

            return $this;
        }

        throw new \LogicException('Invalid compression level');
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     *
     * @return $this
     */
    public function setFile($file)
    {

        if (!is_file($file)) {
            throw new \InvalidArgumentException('Invalid file');
        }

        if (!is_readable($file)) {
            throw new \InvalidArgumentException('Unable to read file');
        }

        //TODO: move this logic to Client.php
        $this->file = curl_file_create($file);
        $this->url  = null;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid url');
        }

        if (false === ($scheme = parse_url($url, PHP_URL_SCHEME))) {
            throw new \InvalidArgumentException('Invalid url');
        }

        if (!in_array($scheme, ['http', 'https'])) {
            throw new \InvalidArgumentException('Invalid url scheme');
        }

        $this->url  = $url;
        $this->file = null;

        return $this;
    }

    /**
     * @return string
     */
    public function getOptimizationMode()
    {
        return $this->optimizationMode;
    }

    /**
     * @param string $optimizationMode
     *
     * @return $this
     * @throws \LogicException
     */
    public function setOptimizationMode($optimizationMode)
    {
        if (in_array($optimizationMode, [self::OPTIMIZATION_LOSSLESS, self::OPTIMIZATION_LOSSY])) {
            $this->optimizationMode = $optimizationMode;

            return $this;
        }

        throw new \LogicException('Invalid optimization mode');
    }

    /**
     * @return string
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * @param string $quality
     *
     * @return $this
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * @return string
     */
    public function getSamplingSchema()
    {
        return $this->samplingSchema;
    }

    /**
     * @param string $samplingSchema
     *
     * @return $this
     * @throws \LogicException
     */
    public function setSamplingSchema($samplingSchema)
    {
        if (in_array($samplingSchema, [self::SAMPLING_SCHEME_420, self::SAMPLING_SCHEME_422, self::SAMPLING_SCHEME_444])) {
            $this->samplingSchema = $samplingSchema;

            return $this;
        }

        throw new \LogicException('Invalid optimization mode');
    }

    /**
     * @return array
     * @throws \LogicException
     */
    public function prepareRequestData()
    {
        if (empty($this->getUrl()) && empty($this->getFile())) {
            throw new \LogicException('File or url must be specified');
        }
        $requestData = [
            'quality'           => $this->getQuality(),
            'compression_level' => $this->getCompressionLevel(),
            'optimization_mode' => $this->getOptimizationMode(),
            'sampling_schema'   => $this->getSamplingSchema(),
        ];
        if ($this->getFile()) {
            $return = ['file' => $this->getFile()];
            $input = array_filter($requestData);
            if ($input) {
                $return['input'] = $input;
            }
            return $return;
        }

        return array_merge(['url' => $this->getUrl()], array_filter($requestData));
    }
}
