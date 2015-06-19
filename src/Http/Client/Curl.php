<?php
/**
 * SocialConnect project
 * @author: Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace SocialConnect\Common\Http\Client;

use SocialConnect\Common\Http\Response;
use SocialConnect\Common\Exception;
use RuntimeException;

class Curl extends Client
{
    /**
     * Curl resource
     *
     * @var resource
     */
    protected $client;

    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new RuntimeException('You need to install curl-ext for use SocialConnect-Http\Client\Curl.');
        }

        $this->client = curl_init();
    }

    /**
     * {@inheritdoc}
     */
    public function request($url, array $parameters = array(), $method = Client::GET)
    {
        switch ($method) {
            case Client::POST:
                curl_setopt($this->client, CURLOPT_POST, true);
                break;
            case Client::GET:
                curl_setopt($this->client, CURLOPT_HTTPGET, true);
                break;
        }

        curl_setopt($this->client, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->client, CURLOPT_URL, $url);
        curl_setopt($this->client, CURLOPT_HEADER, 0);
        curl_setopt($this->client, CURLOPT_USERAGENT, 'SocialConnect-Http-Client-Curl' . curl_version()['version']);

        $result = curl_exec($this->client);
        if (!$result) {
            throw new Exception('Curl http Error');
        }

        $response = new Response(curl_getinfo($this->client, CURLINFO_HTTP_CODE), $result);
        
        /**
         * Reset all options of a libcurl client after request
         */
        curl_reset($this->client);
        
        return $response;
    }
    
    public function setOption($option, $value)
    {
        curl_setopt($this->client, $option, $value);
    }
}
