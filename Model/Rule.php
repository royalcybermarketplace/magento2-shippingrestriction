<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

namespace RoyalCyberMarketplace\ShippingRestriction\Model;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Rule\Model\AbstractModel;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Rule
 */
class Rule extends AbstractModel
{
    protected $_productIds;
    /**
     * Get array of product ids which are matched by rule
     *
     * @return array
     */
    public function getListProductIdsInRule()
    {
        $productCollection = \Magento\Framework\App\ObjectManager::getInstance()->create(
            Collection::class
        );
        $productFactory = \Magento\Framework\App\ObjectManager::getInstance()->create(
            ProductFactory::class
        );
        $this->_productIds = [];
        $this->setCollectedAttributes([]);
        $this->getConditions()->collectValidatedAttributes($productCollection);
        \Magento\Framework\App\ObjectManager::getInstance()->create(
            Iterator::class
        )->walk(
            $this->_productCollection->getSelect(),
            [[$this, 'callbackValidateProduct']],
            [
                'attributes' => $this->getCollectedAttributes(),
                'product' => $productFactory->create()
            ]
        );
        return $this->_productIds;
    }
    /**
     * Callback function for product matching
     *
     * @param array $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);
        $websites = $this->_getWebsitesMap();
        foreach ($websites as $websiteId => $defaultStoreId) {
            $product->setStoreId($defaultStoreId);
            if ($this->getConditions()->validate($product)) {
                $this->_productIds[] = $product->getId();
            }
        }
    }
    /**
     * Prepare website map
     *
     * @return array
     */
    protected function _getWebsitesMap()
    {
        $map = [];
        $websites = \Magento\Framework\App\ObjectManager::getInstance()->create(
            StoreManagerInterface::class
        )->getWebsites();
        foreach ($websites as $website) {
            // Continue if website has no store to be able to create catalog rule for website without store
            if ($website->getDefaultStore() === null) {
                continue;
            }
            $map[$website->getId()] = $website->getDefaultStore()->getId();
        }
        return $map;
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->condCombineFactory->create();
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance()
    {
        return $this->condProdCombineF->create();
    }
}
