<?php

namespace OnTap\MasterCard\Gateway\Http\Client\Adapter;

use Magento\Framework\HTTP\Adapter\Curl;
use Zend_Http_Client;
use Zend_Uri_Exception;
use Zend_Uri_Http;

class Rest extends Curl
{
    /**
     * Send request to the remote server
     *
     * @param string $method
     * @param Zend_Uri_Http|string $url
     * @param string $httpVer
     * @param array $headers
     * @param string $body
     * @return string Request as text
     * @throws Zend_Uri_Exception
     */
    public function write($method, $url, $httpVer = '1.1', $headers = [], $body = '')
    {
        if ($url instanceof Zend_Uri_Http) {
            $url = $url->getUri();
        }
        $this->_applyConfig();

        // set url to post to
        // @codingStandardsIgnoreStart
        curl_setopt($this->_getResource(), CURLOPT_URL, $url);
        curl_setopt($this->_getResource(), CURLOPT_RETURNTRANSFER, true);

        if ($method == Zend_Http_Client::POST) {
            curl_setopt($this->_getResource(), CURLOPT_POST, true);
            curl_setopt($this->_getResource(), CURLOPT_POSTFIELDS, $body);
            $headers[] = 'Content-Length: ' . strlen($body);
        } elseif ($method == Zend_Http_Client::PUT) {
            curl_setopt($this->_getResource(), CURLOPT_CUSTOMREQUEST, Zend_Http_Client::PUT);
            curl_setopt($this->_getResource(), CURLOPT_POSTFIELDS, $body);
            $headers[] = 'Content-Length: ' . strlen($body);
        } elseif ($method == Zend_Http_Client::GET) {
            curl_setopt($this->_getResource(), CURLOPT_HTTPGET, true);
        }

        if (is_array($headers)) {
            curl_setopt($this->_getResource(), CURLOPT_HTTPHEADER, $headers);
        }

        /**
         * @internal Curl options setter have to be re-factored
         */
        $header = isset($this->_config['header']) ? $this->_config['header'] : true;
        curl_setopt($this->_getResource(), CURLOPT_HEADER, $header);
        curl_setopt($this->_getResource(), CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->_getResource(), CURLOPT_SSL_VERIFYHOST, false);
        // @codingStandardsIgnoreStop

        return $body;
    }
}
