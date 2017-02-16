<?php
/**
 * PHP library for Mengene API
 *
 * @copyright Copyright (c) 2017 Hidayet Doğan
 * @license https://opensource.org/licenses/MIT MIT License
 */
namespace Mengene;

class Response
{
    /**
     * @var boolean $status
     */
    protected $status;

    /**
     * HTTP Status Code
     *
     * @var integer $code
     */
    protected $code;

    /**
     * @var array $rawData
     */
    protected $rawData;

    /**
     * @param array     $rawData
     * @param integer   $code
     */
    public function __construct($rawData, $code = 200)
    {
        $this->setStatus(false);
        $this->setCode($code);
        $this->setRawData($rawData);
        if (isset($rawData['status'])) {
            $this->setStatus($rawData['status']);;
        }
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     *
     * @return $this
     */
    protected function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     *
     * @return $this
     */
    protected function setStatus($status)
    {
        $this->status = $status ? true : false;

        return $this;
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param array $rawData
     *
     * @return $this
     */
    protected function setRawData($rawData)
    {
        $this->rawData = $rawData;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        if (isset($this->rawData['message'])) {
            return $this->rawData['message'];
        }

        return;
    }

    public function getDownloadableUrl()
    {
        if (empty($this->rawData['output_url'])) {
            throw new \InvalidArgumentException('Invalid response');
        }

        return $this->rawData['output_url'];
    }

    public function toArray()
    {
        return $this->rawData;
    }
}
