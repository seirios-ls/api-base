<?php

namespace Transmit\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Request
{
    protected $gateway = '';

    protected $path = '';

    //url = gateway + path
    protected $url;

    protected $method = 'GET';

    protected $timeout = 60;

    protected $debug = false;

    protected $headers = [];

    protected $httpErrors = true;

    protected $async = false;

    protected $getParam = [];

    protected $format = '';

    protected $body = [];
    protected $json = [];
    protected $formData = [];

    protected $cookie;

    protected $proxy;

    protected $urlLogTip = '';

    protected $logsDir;

    protected $httpVersion = '1.1';

    protected $sslVerify = false;

    protected $unencrypted = [];


    private $option;

    public function setTimeout(int $timeout): Request
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function setUrl($url): Request
    {
        $this->url = $url;
        return $this;
    }

    public function setDebug(bool $debug): Request
    {
        $this->debug = $debug;
        return $this;
    }

    public function setHttpErrors(bool $httpError): Request
    {
        $this->httpErrors = $httpError;
        return $this;
    }

    public function setHeaders(array $headers): Request
    {
        $this->headers = $headers;
        return $this;
    }

    public function setMethod(string $method): Request
    {
        $this->method = $method;
        return $this;
    }

    public function setAsync(bool $async): Request
    {
        $this->async = $async;
        return $this;
    }

    public function setFormData(array $formData): Request
    {
        $this->formData = $formData;
        return $this;
    }

    public function setJson(array $json): Request
    {
        $this->json = $json;
        return $this;
    }

    public function setProxy(string $proxy): Request
    {
        $this->proxy = $proxy;
        return $this;
    }

    public function setLogsDir($dir): Request
    {
        $this->logsDir = rtrim($dir, '/') . '/';
        return $this;
    }

    public function setHttpVersion(string $httpVersion): Request
    {
        $this->httpVersion = $httpVersion;
        return $this;
    }

    public function setSslVerify(bool $sslVerify): Request
    {
        $this->sslVerify = $sslVerify;
        return $this;
    }

    public function setUnencrypted(array $unencrypted): Request
    {
        $this->unencrypted = $unencrypted;
        return $this;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function setBody(array $body): Request
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param array $getParam
     */
    public function setGetParam(array $getParam): void
    {
        $this->getParam = $getParam;
    }

    public function setCookie($cookie): Request
    {
        $this->cookie = $cookie;
        return $this;
    }

    public function setUrlLogTip(string $urlLogTip): Request
    {
        $this->urlLogTip = $urlLogTip;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @return array
     */
    public function getFormData(): array
    {
        return $this->formData;
    }



    /**
     * @return mixed
     */
    public function getLogsDir()
    {
        return $this->logsDir;
    }

    /**
     * @return string
     */
    public function getUrlLogTip(): string
    {
        return $this->urlLogTip;
    }

    public function request(string $response): Response
    {
        $this->buildUrl();
        $this->buildMethod();
        $this->buildOptions();

        $uuid = sha1(uniqid());

        $this->requestLogs($uuid);

        /**** @var  $response Response */
        $response = new $response;

        try {
            $client = new Client();

            $result = $client->request($this->method, $this->url, $this->option);

            $body = $result->getBody()->getContents();

            $this->responseLogs($uuid, $body);

            $response->setCode($result->getStatusCode())
                ->setHeader($result->getHeaders())
                ->setBody($body)
                ->resolve();
            return $response;
        } catch (GuzzleException $e) {

            $this->responseLogs($uuid, $e->getMessage());

            $response->setCode('500')
                ->setError($e->getMessage());
            return $response;
        }
    }

    private function buildUrl()
    {
        if (!$this->url) {
            $this->url = $this->gateway . $this->path;
        }

        $this->method = strtolower($this->method);
        if ($this->method == 'get' and $this->getParam) {
            $this->url .= '?' . http_build_query($this->getParam);
        }
    }

    private function buildMethod()
    {
        if ($this->async) {
            $this->method .= 'Async';
        }
    }

    private function buildOptions()
    {
        $this->option['verify'] = $this->sslVerify;

        $this->option['http_errors'] = $this->httpErrors;

        if ($this->headers) {
            $this->option['headers'] = $this->headers;
        }

        if ($this->body) {
            $this->option['body'] = $this->body;
        }

        if ($this->cookie) {
            $this->option['cookies'] = $this->cookie;
        }

        if ($this->timeout) {
            $this->option['connect_timeout'] = $this->timeout;
            $this->option['timeout'] = $this->timeout;
        }

        if ($this->debug) {
            $this->option['debug'] = $this->debug;
        }

        if ($this->formData) {
            $this->option['form_params'] = $this->formData;
        }

        if ($this->json) {
            $this->option['json'] = $this->json;
        }

        if ($this->proxy) {
            if (!strpos($this->proxy, '//')) {
                $this->proxy = 'http://' . $this->proxy;
            }

            $this->option['proxy'] = $this->proxy;
        }
    }

    private function requestLogs($uuid)
    {
        if (!$this->logsDir) {
            return;
        }

        $logs = date('Y-m-d H:i:s') . " REQUEST  " . $uuid . " ";
        if ($this->method == 'get') {
            $logs .= $this->url;
        } else {
            if ($this->option) {
                $logs .= json_encode($this->json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                $logs .= $this->url;
            }
        }

        $fileName = $this->logsDir . date('Ym');

        if (!is_dir($fileName)) {
            @mkdir($fileName, 755, true);
        }
        $file = date('Ymd') . '.logs';

        //url tip
        if ($this->urlLogTip) {
            $urlLogs = date('Y-m-d H:i:s') . " URL      " . $uuid . " " . $this->urlLogTip . ' ' . $this->url;
            file_put_contents($fileName . '/' . $file, $urlLogs . "\r\n", FILE_APPEND);
        }

        //待加密数据日记
        if ($this->unencrypted) {
            $orgLogs = date('Y-m-d H:i:s') . " ENCRYPT  " . $uuid . " ";
            $orgLogs .= json_encode($this->unencrypted, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            file_put_contents($fileName . '/' . $file, $orgLogs . "\r\n", FILE_APPEND);
        }

        file_put_contents($fileName . '/' . $file, $logs . "\r\n", FILE_APPEND);
    }

    private function responseLogs($uuid, $data)
    {
        if (!$this->logsDir) {
            return;
        }
        if (!$data) {
            $data = '';
        } elseif (is_array($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } elseif (!is_string($data)) {
            return;
        }

        $logs = date('Y-m-d H:i:s') . " RESPONSE " . $uuid . " ";
        $logs .= $data;

        $fileName = $this->logsDir . date('Ym');

        if (!is_dir($fileName)) {
            @mkdir($fileName, 755, true);
        }

        $file = date('Ymd') . '.logs';
        file_put_contents($fileName . '/' . $file, $logs . "\r\n", FILE_APPEND);
    }
}