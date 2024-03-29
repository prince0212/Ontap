<?php

declare(strict_types=1);

namespace OnTap\MasterCard\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TreeDSecureVersion implements OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Disabled')],
            ['value' => 1, 'label' => __('Enabled 3-D Secure')],
            ['value' => 2, 'label' => __('Enabled EMV 3-D Secure (3DS2)')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            0 => __('Disabled'),
            1 => __('Enabled 3-D Secure'),
            2 => __('Enabled EMV 3-D Secure (3DS2)')
        ];
    }
}
