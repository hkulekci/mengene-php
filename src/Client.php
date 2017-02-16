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
     * Base URL of API service
     *
     * @var string
     */
    public $apiUrl = 'https://api.mengene.io/v1';

    /** @var string */
    private $_apiKey = null;

    /** @var array */
    private $_options = [];

    /** @var int */
    private $_timeout = 60;

    /** @var string */
    private $_userAgent = 'mengene-php/1.0';

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
     * @param  ImageOptions $options
     * @return array
     * @throws \LogicException
     */
    public function process(ImageOptions $options)
    {
        return self::_request('/process', 'POST', $options->prepareRequestData());
    }

    /**
     * @return array
     */
    public function status()
    {
        return self::_request('/status');
    }

    /**
     * Downloads processed image
     *
     * @param array $result Result array returned from `process()` method.
     * @param string $path Local file path
     * @return void
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function download(array $result, $path)
    {
        if (empty($result['output_url'])) {
            throw new \InvalidArgumentException('Invalid response');
        }

        if (false === ($fp = @fopen($path, 'w'))) {
            throw new \Exception('Unable to create file');
        }

        if (false === ($ch = curl_init($result['output_url']))) {
            throw new \Exception('Unable to init curl');
        }

        curl_setopt_array($ch, [
            CURLOPT_FAILONERROR => 1,
            CURLOPT_FILE => $fp,
            CURLOPT_USERAGENT => $this->_userAgent,
        ]);

        curl_exec($ch);

        if (curl_errno($ch) > 0) {
            @fclose($fp);
            @unlink($path);
            throw new \Exception('Download is failed with error: ' . curl_error($ch));
        }

        curl_close($ch);

        if (false === @fclose($fp)) {
            @unlink($path);
            throw new \Exception('Unable to close file pointer');
        }

        return;
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
            throw new ClientFailedException('Unable to init curl');
        }

        $headers = [
            "X-API-Key: {$this->_apiKey}",
        ];

        if ($method === 'GET' || isset($data['url'])) {
            $headers[] = 'Content-Type: application/json';
        }

        $curlOptions = [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_USERAGENT => $this->_userAgent,
            CURLOPT_FAILONERROR => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => $this->_timeout,

        ];

        if ($method === 'POST') {
            $curlOptions += [
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => isset($data['url']) ? json_encode($data) : $data,
            ];
        }

        curl_setopt_array($ch, $curlOptions);

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

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (false === $result['success']) {
            throw new ClientFailedException($result['message'], $code);
        }

        curl_close($ch);

        return $result;
    }
}
