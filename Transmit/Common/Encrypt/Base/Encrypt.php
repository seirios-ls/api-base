<?php


namespace CMB\Common\Encrypt\Base;

use http\Exception\BadMethodCallException;

class Encrypt
{

    /**
     * 加密类型
     * @var string
     */
    protected $signType = '';

    /**
     * 待加密参数
     * @var array
     */
    protected $params = [];

    /**
     * 待加密字符串
     * @var string
     */
    protected $toSignString = '';

    /**
     * 签名字符串
     * @var string
     */
    protected $signString = '';

    /**
     * @param array $params
     * @return Encrypt
     */
    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    /**
     * 签名
     * @return mixed
     */
    public function sign()
    {
        throw new BadMethodCallException('');
    }
}