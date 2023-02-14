<?php

namespace OnTap\MasterCard\Block\Adminhtml\Order\View\Info;

class Ach extends Details
{
    /**
     * @var array
     */
    protected $applicableMethods = [
        \OnTap\MasterCard\Model\Ui\Ach\ConfigProvider::METHOD_CODE,
    ];
}
