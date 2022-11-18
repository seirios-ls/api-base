<?php

namespace Transmit\Common;

use Transmit\Http\Parameter;
use Transmit\Http\Request;
use Transmit\Http\Response;
use Exception;
use http\Exception\BadMethodCallException;

/**
 * 执行请求基础类
 */
class Transmit extends Construct
{
    /**
     * 请求类
     * @var Request
     */
    private $request;

    /**
     * 执行请求--构建请求参数
     * @param Parameter $parameter
     * @return Response
     * @throws Exception
     * @author xis
     */
    public function execute(Parameter $parameter): Response
    {
        //仅仅是dome
        //需要继承后重写
        try {
            $parameter->setParameter($this->generate($parameter));

            $this->sign($parameter);

            return $this->request($parameter);
        } catch (Exception $exception) {
            return $this->exceptionResponse($parameter->getResponse(), $exception->getMessage());
        }
    }

    /**
     * 请求参数生成
     * @param Parameter $parameter
     * @return array
     */
    public function generate(Parameter $parameter): array
    {
        throw new BadMethodCallException(self::class . ':generate not be inheritance');
    }

    /**
     * 生成签名
     * @param Parameter $parameter
     * @return mixed
     */
    public function sign(Parameter $parameter)
    {
        throw new BadMethodCallException(self::class . ':sign not be inheritance');
    }

    public function exceptionResponse($response, $errorMessage): Response
    {
        if (!class_exists($response)) {
            $response = new Response();
        } else {
            $response = new $response;
        }

        $response->setError($errorMessage);

        return $response;
    }

    /**
     * 执行请求--构建执行参数
     * @param Parameter $parameter
     * @return Response
     */
    protected function request(Parameter $parameter): Response
    {
        //初始化
        if (!$this->request instanceof Request) {
            $this->request = new Request;
            $this->request->setTimeout($this->timeout);
            $this->request->setDebug($this->httpDebug);
            $this->request->setLogsDir($this->logsDir);

            $this->request->setHeaders($parameter->getHeaders());
        }

        if ($parameter->getHeaders()) {
            $this->request->setHeaders($parameter->getHeaders());
        }
        $this->request->setUrlLogTip($parameter->getUrlLogTip());

        $param = $parameter->generate();

        $client = $this->request;

        $client->setUrl($this->url);

        $client->setMethod($parameter->getMethod());

        //get
        if (strtoupper($parameter->getMethod()) == 'GET') {
            $client->setGetParam($param);
        }
        //post
        if (strtoupper($parameter->getMethod()) == 'POST') {

            if ($parameter->getFormat()) {
                $this->format = $parameter->getFormat();
            }

            if ($this->format == 'json') {
                $client->setJson($param);
            }
            if ($this->format == 'formData') {
                $client->setFormData($param);
            }
            if ($this->format == 'body') {
                $client->setBody($param);
            }
        }

        return $client->request($parameter->getResponse());
    }
}
