<?php

namespace OnTap\MasterCard\Block\Adminhtml\Order\View\Info;

class Card extends Details
{
    /**
     * @var array
     */
    protected $applicableMethods = [
        \OnTap\MasterCard\Model\Ui\Hpf\ConfigProvider::METHOD_CODE,
        \OnTap\MasterCard\Model\Ui\Hosted\ConfigProvider::METHOD_CODE,
    ];
}
