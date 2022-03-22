<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

declare(strict_types=1);

namespace RoyalCyberMarketplace\ShippingRestriction\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->scopeConfig=$scopeConfig;
        $this->_storeManager=$storeManager;
    }

    /**
     * @return bool
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue('royalcybermarketplaceshippingrestriction/general/'. $code, $storeId);
    }
    /**
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->getGeneralConfig('enabled') == 1) {
            return true;
        } else {
            return false;
        }
    }
}
