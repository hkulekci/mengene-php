<?php
namespace Mengene;

class Client
{
    const COMPRESSION_HIGH = 'high';
    const COMPRESSION_MEDIUM = 'medium';
    const COMPRESSION_LOW = 'low';

    const OPTIMIZATION_LOSSY = 'lossy';
    const OPTIMIZATION_LOSSLESS = 'lossless';

    const SCHEME_444 = '4:4:4';
    const SCHEME_422 = '4:2:2';
    const SCHEME_420 = '4:2:0';

    public $apiUrl = 'http://localhost:8888/v1';

    private $_apiKey = null;
    private $_options = [];
    private $_timeout = 60;

    /**
     * @param string|null $apiKey
     */
    public function __construct($apiKey = null)
    {
        if ($apiKey !== null) {
            $this->setApiKey($apiKey);
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function status()
    {
        return self::_request('/status');
    }

    /**
     * @param string $filename
     * @param array $options
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function upload($filename, array $options = [])
    {
        if (empty($filename)) {
            throw new \InvalidArgumentException();
        }

        if (!is_readable($filename)) {
        }

        $data = [
            'file' => curl_file_create($filename),
            'input' => array_merge($this->_options, $options),
        ];

        return self::_request('/upload', 'POST', $data);
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
     * @param string $mode
     * @return self
     */
    public function setOptimizationMode($mode)
    {
        $this->_options['optimization-mode'] = $mode;

        return $this;
    }

    /**
     * @param integer $quality
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setQuality($quality)
    {
        if ($quality < 1 || $quality > 100) {
            throw new \InvalidArgumentException('Invalid quality');
        }

        $this->_options['quality'] = $quality;

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
     * @param integer $seconds
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
     * @throws \Exception
     */
    private function _request($endpoint, $method = 'GET', array $data = [])
    {
        if (false === ($ch = curl_init($this->apiUrl . $endpoint))) {
            return new \Exception('cURL Error: Unable to init curl');
        }

        $headers = [
            "X-API-Key: {$this->_apiKey}",
        ];

        if ($method === 'GET') {
            $headers[] = 'Content-Type: application/json';
        }
        else {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MengenePHP/1.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);

        if (false === ($response = curl_exec($ch))) {
            throw new \Exception('cURL Error: ' . curl_error($ch));
        }

        curl_close($ch);

        if (null === ($result = json_decode($response, true))) {
            throw new \Exception('JSON Error: ' . json_last_error_msg());
        }

        if (false === $result['success']) {
            throw new \Exception($result['message'], curl_getinfo($ch, CURLINFO_HTTP_CODE));
        }

        return $result;
    }
}
