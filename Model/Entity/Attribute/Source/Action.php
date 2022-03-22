<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

namespace RoyalCyberMarketplace\ShippingRestriction\Model\Entity\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Action extends AbstractSource
{

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        return [
            [
                'value' => 1,
                'label' => __('Backend Order')
            ],
            [
                'value' => 0,
                'label' => __('Frontend Order')
            ],
        ];
    }
}
