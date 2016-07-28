<?php
namespace HappyDemon\UitDatabank;

use HappyDemon\UitDatabank\Exception as UitDatabankException;
use HappyDemon\UitDatabank\OAuth\TwoLegged;
use HappyDemon\UitDatabank\Search\Filter;
use HappyDemon\UitDatabank\Search\Result;

/**
 * UitDatabank class
 *
 * @author    Maxim Kerstens<maxim@happydemon.xyz>
 * @author    Tijs Verkoyen <php-uitdatabank@verkoyen.eu>
 * @version   3.0.0
 * @copyright Copyright (c), Tijs Verkoyen. All rights reserved.
 * @license   BSD License
 */
class UitDatabank
{
    const VERSION = "3.0.0";

    const DEBUG = true;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $server;

    /**
     * The user agent
     *
     * @var string
     */
    private $userAgent;

    /**
     * The timeout
     *
     * @var int
     */
    private $timeout = 30;

    /**
     * Constructor
     *
     * @param string $key    The key to be used for authenticating
     * @param string $secret The secret to be used for authenticating
     * @param string $server The server to connect to
     */
    public function __construct($key, $secret, $server)
    {
        $this->setKey($key);
        $this->setSecret($secret);
        $this->setServer($server);
    }

    /**
     * Set the key that will be used
     *
     * @param string $key
     */
    protected function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get the key that will be used
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the secret that will be used
     *
     * @param string $secret
     */
    protected function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Get the secret that will be used
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set the server that will be used
     *
     * @param string $server
     */
    protected function setServer($server)
    {
        $this->server = rtrim($server, '/');
    }

    /**
     * Get the server that will be used
     *
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Get the useragent that will be used.
     * Our version will be prepended to yours.
     * It will look like: "PHP UitDatabank/<version> <your-user-agent>"
     *
     * @return string
     */
    public function getUserAgent()
    {
        return (string) 'PHP UitDatabank/' . self::VERSION . ' ' . $this->userAgent;
    }

    /**
     * Set the user-agent for you application
     * It will be appended to ours, the result will look like: "PHP UitDatabank/<version> <your-user-agent>"
     *
     * @param string $userAgent Your user-agent, it should look like <app-name>/<app-version>.
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = (string) $userAgent;
    }
    
    /**
     * Make a call
     *
     * @param string     $url
     * @param array|null $parameters
     * @param string     $method
     *
     * @return mixed
     * @throws \HappyDemon\UitDatabank\Exception
     */
    protected function doCall($url, $parameters = null, $method = 'GET')
    {
        $oAuth = new TwoLegged(
            $this->getKey(),
            $this->getSecret()
        );

        foreach ($parameters as $key => $value) {
            $oAuth->addParameter($key, $value);
        }

        $url = $this->getServer() . '/' . ltrim($url, '/');
        $oAuth->setUrl($url);

        $headers = array(
            $oAuth->getAuthorizationHeader(),
        );

        $urlToCall = $url;
        if ($method == 'GET') {
            if (!empty($parameters)) {
                $urlToCall .= '?' . http_build_query($parameters);
            }
        }

        // set options
        $options[CURLOPT_URL] = $urlToCall;
        $options[CURLOPT_USERAGENT] = $this->getUserAgent();
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
            $options[CURLOPT_FOLLOWLOCATION] = true;
        }
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_SSL_VERIFYHOST] = false;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        $options[CURLOPT_HTTPHEADER] = $headers;

        // init
        $curl = curl_init();

        // set options
        curl_setopt_array($curl, $options);

        // execute
        $response = curl_exec($curl);

        // fetch errors
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        if ($errorNumber != 0) {
            throw new UitDatabankException($errorMessage, $errorNumber);
        }

        return $response;
    }

    /**
     * @param $suffix
     * @return string
     */
    protected function buildUrl($suffix)
    {
        return $this->getServer() . '/' . trim($suffix, '/');
    }
    
    /**
     * Search the database
     *
     * @param Filter $filter
     *
     * @return \HappyDemon\UitDatabank\Search\Result
     * @throws \HappyDemon\UitDatabank\Exception
     */
    public function search(Filter $filter)
    {
        $parameters = $filter->buildForRequest();
        $response = $this->doCall('/searchv2/search', $parameters);
        $xml = simplexml_load_string($response);

        if ($xml !== false) {
            $xml = $xml->children('http://www.cultuurdatabank.com/XMLSchema/CdbXSD/3.2/FINAL');
            if (empty($xml)) {
                throw new UitDatabankException('Invalid response.');
            }
        } else {
            throw new UitDatabankException('Invalid response.');
        }

        return Result::createFromXML($xml);
    }
}
