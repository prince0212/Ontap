<?php

namespace OnTap\MasterCard\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use OnTap\MasterCard\Helper\Data;

class AbstractModel
{
    /**
     * @var ScopeConfigInterface
     */
    public $scopeConfig;
    /**
     * @var CartRepositoryInterface
     */
    public $quoteRepository;
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * Initialization
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param CartRepositoryInterface $quoteRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface    $scopeConfig,
        CartRepositoryInterface $quoteRepository,
        StoreManagerInterface   $storeManager
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->quoteRepository = $quoteRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @return bool
     */
    public function getEnable3DSecure()
    {
        return $this->scopeConfig->getValue(Data::XML_PATH_ENABLE_3DSECURE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Process the curl request
     *
     * @param mixed $payLoad
     * @param string $apiUrl
     * @param string $method
     * @return bool|string
     */
    public function processCurlRequest($payLoad, string $apiUrl, string $method = '')
    {
        $user = $this->getUser();
        $password = $this->getPassword();

        // Initialize the curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getApiUrl() . $apiUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERPWD, "$user:$password");

        //checking the condition for the payload
        if (!empty($payLoad)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payLoad, true));
        }

        //if the API request method is put
        if ($method === "PUT") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        }

        //if the API request method is delete
        if ($method === "DELETE") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $result = curl_exec($ch);

        if ($result === false) {
            return curl_error($ch);
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return 'merchant.' . $this->getUserName();
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->scopeConfig->getValue(Data::XML_PATH_API_USER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->scopeConfig->getValue(Data::XML_PATH_API_PASSWORD, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        switch ($this->getApiGateway()):
            case 'api_eu':
                $baseUrl = $this->scopeConfig->getValue(Data::XML_PATH_API_URL_EU, ScopeInterface::SCOPE_STORE);
                return $this->buildUrl($baseUrl, $this->getApiVersion(), $this->getUserName());
            case 'api_as':
                $baseUrl = $this->scopeConfig->getValue(Data::XML_PATH_API_URL_AS, ScopeInterface::SCOPE_STORE);
                return $this->buildUrl($baseUrl, $this->getApiVersion(), $this->getUserName());
            case 'api_uat':
                $baseUrl = $this->scopeConfig->getValue(Data::XML_PATH_API_URL_UAT, ScopeInterface::SCOPE_STORE);
                return $this->buildUrl($baseUrl, $this->getApiVersion(), $this->getUserName());
            case 'api_na';
                $baseUrl = $this->scopeConfig->getValue(Data::XML_PATH_API_URL_NA, ScopeInterface::SCOPE_STORE);
                return $this->buildUrl($baseUrl, $this->getApiVersion(), $this->getUserName());
            case 'api_other':
                $baseUrl = $this->scopeConfig->getValue(Data::XML_PATH_API_OTHER_URL, ScopeInterface::SCOPE_STORE);
                return $this->buildUrl($baseUrl, $this->getApiVersion(), $this->getUserName());
        endswitch;
    }

    /**
     * @return string
     */
    public function getApiGateway()
    {
        return $this->scopeConfig->getValue(Data::XML_PATH_API_GATEWAY, ScopeInterface::SCOPE_STORE);
    }

    /**
     *
     * @param string $baseUrl
     * @param string $apiVersion
     * @param string $userName
     * @return string
     */
    protected function buildUrl(string $baseUrl, string $apiVersion, string $userName)
    {
        return $baseUrl . "api/rest/version/" . $apiVersion . "/merchant/" . $userName;
    }

    /**
     * @return int
     */
    public function getApiVersion()
    {
        return $this->scopeConfig->getValue(Data::XML_PATH_API_VERSION, ScopeInterface::SCOPE_STORE);
    }
}
