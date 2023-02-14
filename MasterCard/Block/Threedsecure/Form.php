<?php

namespace OnTap\MasterCard\Block\Threedsecure;

use Magento\Framework\Url;
use Magento\Framework\View\Element\Template;
use OnTap\MasterCard\Gateway\Request\ThreeDSecure\CheckDataBuilder;

class Form extends Template
{
    /**
     * @return string
     */
    public function getReturnUrl()
    {
        /** @var Url $urlBuilder */
        $urlBuilder = $this->_urlBuilder;

        return $urlBuilder->setUseSession(true)->getUrl(
            CheckDataBuilder::RESPONSE_URL,
            [
                '_secure' => true,
                '_query' => [
                    CheckDataBuilder::RESPONSE_SID_PARAMETER => $this->_session->getSessionId(),
                ],
            ]
        );
    }
}
