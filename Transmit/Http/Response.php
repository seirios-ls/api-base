<?php

namespace Transmit\Http;

class Response
{

    private $code;

    private $body;

    private $header;

    private $success = false;

    private $error;

    public function __construct($code = 500, $body = '', $header = '')
    {
        $this->code = $code;
        $this->header = $header;
        $this->body = $body;

        if ($this->code == 200) {
            $this->success = true;
        }
    }

    public static function generate($code, $body, $header): Response
    {
        return (new self($code, $body, $header));
    }

    public function resolve(): Response
    {
        if ($contentType = $this->getHeader()['Content-Type'][0] ?? '') {
            $contentType = strtolower($contentType);
            if (strpos($contentType, 'gbk')) {
                $this->body = mb_convert_encoding($this->body, 'UTF-8', 'GBK');
            }
        }
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode($code): Response
    {
        $this->code = $code;
        if ($this->code == 200) {
            $this->success = true;
        }
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function get($string)
    {
        return $this->body[$string] ?? [];
    }

    public function setBody($body): Response
    {
        $this->body = $body;
        return $this;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function setHeader($header): Response
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success == true;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     */
    public function setError($error): Response
    {
        $this->error = $error;
        $this->success = false;
        return $this;
    }

}