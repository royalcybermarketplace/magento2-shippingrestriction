<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

namespace RoyalCyberMarketplace\ShippingRestriction\Model\ResourceModel\ShippingFilter;

use RoyalCyberMarketplace\ShippingRestriction\Model\ResourceModel\AbstractCollection;
use RoyalCyberMarketplace\ShippingRestriction\Api\Data\ShippingFilterInterface;
use Magento\Store\Model\Store;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'rule_id';
    /**
     * @var bool
     */
    private $_previewFlag;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \RoyalCyberMarketplace\ShippingRestriction\Model\ShippingFilter::class,
            \RoyalCyberMarketplace\ShippingRestriction\Model\ResourceModel\ShippingFilter::class
        );
        $this->_map['fields']['rule_id'] = 'main_table.rule_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
        $shpfilterCSGId = 'royalcybermarketplace_shippingfilter_customer_group.customer_group_id';
        $this->_map['fields']['customer_group'] = $shpfilterCSGId;
    }

     /**
      * Add filter by store
      *
      * @param int|array|\Magento\Store\Model\Store $store
      * @param bool $withAdmin
      * @return $this
      */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }
    
    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(ShippingFilterInterface::class);
        $this->performAfterLoad('royalcybermarketplace_shippingfilter_store', $entityMetadata->getLinkField());
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }
    
    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metadataPool->getMetadata(ShippingFilterInterface::class);
        $this->joinStoreRelationTable('royalcybermarketplace_shippingfilter_store', $entityMetadata->getLinkField());
    }
}
