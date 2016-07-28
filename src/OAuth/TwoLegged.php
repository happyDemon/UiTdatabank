<?php

namespace HappyDemon\UitDatabank\OAuth;

class TwoLegged
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $signatureMethod = 'HMAC-SHA1';
    protected $timestamp;
    protected $nonce;
    protected $version = '1.0';
    protected $parameters = array();
    protected $httpMethod = 'GET';
    protected $url;

    public function __construct($consumerKey, $consumerSecret)
    {
        $this->setConsumerKey($consumerKey);
        $this->setConsumerSecret($consumerSecret);
    }

    /**
     * @param mixed $consumerKey
     */
    protected function setConsumerKey($consumerKey)
    {
        $this->consumerKey = $consumerKey;
    }

    /**
     * @return mixed
     */
    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    /**
     * @param mixed $consumerSecret
     */
    protected function setConsumerSecret($consumerSecret)
    {
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * @return mixed
     */
    public function getConsumerSecret()
    {
        return $this->consumerSecret;
    }

    /**
     * @param string $httpMethod
     */
    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @param mixed $nonce
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
    }

    /**
     * @return mixed
     */
    public function getNonce()
    {
        if (!$this->nonce) {
            $this->setNonce(md5(microtime() . rand()));
        }

        return $this->nonce;
    }

    public function addParameter($key, $value = null)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $signatureMethod
     */
    public function setSignatureMethod($signatureMethod)
    {
        $this->signatureMethod = $signatureMethod;
    }

    /**
     * @return string
     */
    public function getSignatureMethod()
    {
        return $this->signatureMethod;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        if (!$this->timestamp) {
            $this->setTimestamp(time());
        }

        return $this->timestamp;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Get the normalized parameter string
     *
     * @return string
     */
    protected function getNormalizedParameterString()
    {
        $chunks = array();
        $parameters = (array) $this->getNormalizedParameters();
        foreach ($parameters as $key => $value) {
            $chunks[] = $key . '=' . $value;
        }

        return implode('&', $chunks);
    }

    /**
     * Get the OAuth parameters
     *
     * @param bool $includeSignature Should the signature be included?
     * @return array
     */
    protected function getOAuthParameters($includeSignature = false)
    {
        $parameters = array();
        $parameters['oauth_consumer_key'] = $this->getConsumerKey();
        $parameters['oauth_signature_method'] = $this->getSignatureMethod();
        $parameters['oauth_timestamp'] = $this->getTimestamp();
        $parameters['oauth_nonce'] = $this->getNonce();
        $parameters['oauth_version'] = $this->getVersion();

        if ($includeSignature) {
            $parameters['oauth_signature'] = $this->getSignature();
        }

        return $parameters;
    }

    /**
     * Get the normalized parameters
     *
     * @return array
     */
    protected function getNormalizedParameters()
    {
        $parameters = array_merge(
            $this->getParameters(),
            $this->getOAuthParameters()
        );

        $encoded = array();

        foreach ($parameters as $key => $value) {
            $encodedKey = static::percentEncode($key);
            $encoded[$encodedKey] = static::percentEncode($value);
        }

        ksort($encoded);

        return $encoded;
    }

    /**
     * Get the signature base string
     *
     * @return string
     */
    protected function getSignatureBaseString()
    {
        $chunks = array();
        $chunks[] = strtoupper($this->getHttpMethod());
        $chunks[] = static::percentEncode($this->getUrl());
        $chunks[] = static::percentEncode($this->getNormalizedParameterString());

        return implode('&', $chunks);
    }

    /**
     * Get the signature
     *
     * @return string
     */
    public function getSignature()
    {
        switch ($this->getSignatureMethod()) {
            case 'HMAC-SHA1':
                $signature = $this->getHMACSHA1Signature();
        }

        return $signature;
    }

    /**
     * Get the signature based on the HMAC-SHA1-method
     *
     * @return string
     */
    protected function getHMACSHA1Signature()
    {
        return base64_encode(
            hash_hmac(
                'sha1',
                $this->getSignatureBaseString(),
                $this->getConsumerSecret() . '&',
                true
            )
        );
    }

    /**
     * Get the authorization header
     *
     * @return string
     */
    public function getAuthorizationHeader()
    {
        $parts = parse_url($this->url);
        $header = 'Authorization: OAuth realm="';

        // append url without parameters
        $header .= $parts['scheme'] . '://' . $parts['host'];
        if (isset($parts['path'])) {
            $header .= $parts['path'];
        }
        $header .= '"';

        // append OAuth parameters
        $oAuthParameters = $this->getOAuthParameters(true);
        $chunks = array();
        foreach ($oAuthParameters as $key => $value) {
            $chunks[] = self::percentEncode($key) . '="' . self::percentEncode($value) . '"';
        }

        $header .= ',' . implode(',', $chunks);

        return $header;
    }

    private static function percentEncode($value)
    {
        return rawurlencode($value);
    }
}
