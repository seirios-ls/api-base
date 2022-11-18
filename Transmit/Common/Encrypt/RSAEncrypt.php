<?php

namespace CMB\Common\Encrypt;

use CMB\Common\Encrypt\Base\Encrypt;
use Exception;

/***
 * RSA 签名
 */
class RSAEncrypt extends Encrypt
{

    /**
     * 私钥
     * @var string
     */
    private $privateKey = '';

    /**
     * 私钥文件
     * @var string
     */
    private $privateKeyFile = '';

    protected $signType = 'RSA2';

    /**
     *
     * @return void
     */
    public function SignTypeRSA(): self
    {
        $this->signType = 'RSA';
        return $this;
    }

    /**
     * @return void
     */
    public function signTypeRSA2(): self
    {
        $this->signType = 'RSA2';
        return $this;
    }

    /**
     * 签名
     * @throws Exception
     * @author xis
     */
    public function sign(): string
    {
        if (!$this->params) {
            return '';
        }
        $this->params = array_filter($this->params);

        if (isset($this->params['sign_type'])) {
            $this->params['sign_type'] = $this->signType;
        }

        $this->toSignString();
        $this->signing();
        return $this->signString;
    }

    /**
     * 待签名字符串
     * @author xis
     */
    public function toSignString()
    {
        $params = $this->params;

        ksort($params);
        unset($params['sign']);

        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if ("@" != substr($v, 0, 1)) {

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        $this->toSignString = $stringToBeSigned;
    }

    /**
     * @throws Exception
     */
    public function signing()
    {
        if (!$this->privateKeyFile and $this->privateKey) {
            $priKey = $this->privateKey;
            $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
                wordwrap($priKey, 64, "\n", true) .
                "\n-----END RSA PRIVATE KEY-----";
        } elseif ($this->privateKeyFile) {
            $priKey = file_get_contents($this->privateKeyFile);
            $res = openssl_get_privatekey($priKey);
        } else {
            throw new Exception('请配置秘钥');
        }

        if ("RSA2" == $this->signType) {
            openssl_sign($this->toSignString, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($this->toSignString, $sign, $res);
        }

        if ($this->privateKeyFile) {
            openssl_free_key($res);
        }
        $sign = base64_encode($sign);

        $this->signString = $sign;
    }
}
