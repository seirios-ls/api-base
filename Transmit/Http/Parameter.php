<?php

namespace Transmit\Http;

class Parameter extends Request
{
    protected $response = '';

    protected $path = '';

    protected $parameter;

    public function getParameter()
    {
        return $this->parameter;
    }

    public function setParameter($parameter)
    {
        $this->parameter = $parameter;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    //构建请求参数
    public function generate(): array
    {
        return [];
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
