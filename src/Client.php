<?php
/**
 * PHP library for Mengene API
 *
 * @copyright Copyright (c) 2017 Hidayet Doğan
 * @license https://opensource.org/licenses/MIT MIT License
 */
namespace Mengene;

use Mengene\Exception\ClientFailedException;
use Mengene\Exception\ClientTimedOutException;

class Client
{
    /**
     * Compression levels
     */
    const COMPRESSION_HIGH = 'high';
    const COMPRESSION_MEDIUM = 'medium';
    const COMPRESSION_LOW = 'low';

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
     * Base URL of API service
     *
     * @var string
     */
    public $apiUrl = 'http://localhost:8888/mengene-api/v1';

    /** @var string */
    private $_apiKey = null;

    /** @var array */
    private $_options = [];

    /** @var int */
    private $_timeout = 60;

    /**
     * @param null|string $apiKey
     */
    public function __construct($apiKey = null)
    {
        if ($apiKey !== null) {
            $this->setApiKey($apiKey);
        }
    }

    /**
     * @param array $options
     * @return array
     * @throws \LogicException
     */
    public function process(array $options = [])
    {
        unset($options['file'], $options['url']);

        $options = $this->_options + $options;

        if (isset($options['file'])) {
            $endpoint = '/upload';
            $file = $options['file'];

            unset($options['file']);

            $data = [
                'file' => $file,
                'input' => $options,
            ];
        } else if (isset($options['url'])) {
            $endpoint = '/url';
            $data = $options;
        } else {
            throw new \LogicException('File or url must be specified');
        }

        return self::_request($endpoint, 'POST', $data);
    }

    /**
     * @return array
     */
    public function status()
    {
        return self::_request('/status');
    }

    /**
     * @param string $apiKey
     * @return self
     */
    public function setApiKey($apiKey)
    {
        $this->_apiKey = $apiKey;

        return $this;
    }

    /**
     * @param string $level
     * @return self
     */
    public function setCompressionLevel($level)
    {
        $this->_options['compression-level'] = $level;

        return $this;
    }

    /**
     * @param string $path
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setLocalImage($path)
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException('Invalid file');
        }

        if (!is_readable($path)) {
            throw new \InvalidArgumentException('Unable to read file');
        }

        unset($this->_options['url']);

        $this->_options['file'] = curl_file_create($path);

        return $this;
    }

    /**
     * @param string $mode
     * @return self
     */
    public function setOptimizationMode($mode)
    {
        $this->_options['optimization-mode'] = $mode;

        return $this;
    }

    /**
     * @param int $quality
     * @return self
     */
    public function setQuality($quality)
    {
        $this->_options['quality'] = $quality;

        return $this;
    }

    /**
     * @param string $url
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setRemoteImage($url)
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

        unset($this->_options['file']);

        $this->_options['url'] = $url;

        return $this;
    }

    /**
     * @param string $scheme
     * @return self
     */
    public function setSamplingScheme($scheme)
    {
        $this->_options['sampling-scheme'] = $scheme;

        return $this;
    }

    /**
     * @param int $seconds
     * @return self
     */
    public function setTimeout($seconds)
    {
        $this->_timeout = $seconds;

        return $this;
    }

    /**
     * @param string $endpoint
     * @param string $method
     * @param array $data
     * @return array
     * @throws ClientFailedException
     * @throws ClientTimedOutException
     */
    private function _request($endpoint, $method = 'GET', array $data = [])
    {
        if (false === ($ch = curl_init($this->apiUrl . $endpoint))) {
            throw new ClientFailedException('cURL Error: Unable to init curl');
        }

        $headers = [
            "X-API-Key: {$this->_apiKey}",
        ];

        if ($method === 'GET') {
            $headers[] = 'Content-Type: application/json';
        } else {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array_filter($data));
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MengenePHP/1.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);

        if (false === ($response = curl_exec($ch))) {
            if (curl_errno($ch) === CURLE_OPERATION_TIMEOUTED) {
                throw new ClientTimedOutException(curl_error($ch));
            } else {
                throw new ClientFailedException('cURL Error: ' . curl_error($ch));
            }
        }

        if (null === ($result = json_decode($response, true))) {
            throw new ClientFailedException('JSON Error: ' . json_last_error_msg());
        }

        if (false === $result['success']) {
            throw new ClientFailedException($result['message'], curl_getinfo($ch, CURLINFO_HTTP_CODE));
        }

        curl_close($ch);

        return $result;
    }
}
