<?php

namespace Transmit\Common;

/**
 * 应用基础类
 */
class Construct
{

    /**
     * 网关
     * @var string $gateway
     */
    protected $gateway = '';

    /**
     * 地址
     * @var string $url
     */
    protected $url = '';

    /**
     * 超时时间
     * @var int $timeout
     */
    protected $timeout = 5;

    /**
     * 编码格式
     * @var string $charset
     */
    protected $charset = 'utf-8';

    /**
     * 加密格式
     * @var string $signType
     */
    protected $signType = 'RSA2';

    /**
     * 请求模式 json,formData
     * @var string $format
     */
    protected $format = 'json';

    /**
     * httpDebug
     * @var bool $httpDebug
     */
    protected $httpDebug = false;

    /**
     * logsDir
     * @var string $logsDir
     */
    protected $logsDir = '';

    /**
     * proxy
     * @var string $proxy
     */
    protected $proxy = '';

    /**
     * @param string $gateway
     * @return Construct
     */
    public function setGateway(string $gateway): self
    {
        $this->gateway = $gateway;
        return $this;
    }

    /**
     * @param int $timeout
     * @return Construct
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @param string $charset
     * @return Construct
     */
    public function setCharset(string $charset): self
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * @param string $signType
     * @return Construct
     */
    public function setSignType(string $signType): self
    {
        $this->signType = $signType;
        return $this;
    }

    /**
     * @param string $format
     * @return Construct
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @param bool $httpDebug
     * @return Construct
     */
    public function setHttpDebug(bool $httpDebug): self
    {
        $this->httpDebug = $httpDebug;
        return $this;
    }

    /**
     * @param string $logsDir
     * @return Construct
     */
    public function setLogsDir(string $logsDir): self
    {
        $this->logsDir = $logsDir;
        return $this;
    }

    /**
     * @param string $proxy
     * @return Construct
     */
    public function setProxy(string $proxy): self
    {
        $this->proxy = $proxy;
        return $this;
    }
}
