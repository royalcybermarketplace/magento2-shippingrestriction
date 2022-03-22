<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */
 
namespace RoyalCyberMarketplace\ShippingRestriction\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\MetadataPool;
use RoyalCyberMarketplace\ShippingRestriction\Api\Data\ShippingFilterInterface;
use Magento\Framework\Exception\LocalizedException;

class ShippingFilter extends AbstractDb
{

    /**
     * Store model
     *
     * @var null|Store
     */
    protected $_store = null;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @param Context $context
     * @param MetadataPool $metadataPool
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->metadataPool = $metadataPool;
    }

    protected function _construct()
    {
        $this->_init('royalcybermarketplace_shippingfilter', 'rule_id');
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(ShippingFilterInterface::class)->getEntityConnection();
    }

    /**
     * Process page data after saving
     *
     * @param AbstractModel $object
     * @return $this
     * @throws LocalizedException
     */
    protected function _afterSave($rule)
    {
        $this->_saveShippingCustomer($rule);
        $this->_saveShippingStores($rule);
        return parent::_afterSave($rule);
    }

    protected function _saveShippingCustomer($rule)
    {
        $id = $rule->getId();
        $connection = $this->getConnection();
        $customerGroup = $rule->getCustomerGroups();
        $oldcustomerGroup = $this->lookupCustomerGroupIds($rule->getId());
        if (!empty($customerGroup)) {
            $data = [];
            foreach ($customerGroup as $customerGroupId) {
                $data[] = [
                    'rule_id' => (int)$id,
                    'customer_group_id' => (int)$customerGroupId,
                ];
            }
            $table = $this->getTable('royalcybermarketplace_shippingfilter_customer_group');
            $insert = array_diff($customerGroup, $oldcustomerGroup);
            $delete = array_diff($oldcustomerGroup, $customerGroup);
            if ($delete) {
                $where = ['rule_id = ?' => (int)$rule->getId(), 'customer_group_id IN (?)' => $delete];
                $connection->delete($table, $where);
            }
            if ($insert) {
                $data = [];
                foreach ($insert as $storeId) {
                    $data[] = ['rule_id' => (int)$rule->getId(), 'customer_group_id' => (int)$storeId];
                }
                $connection->insertMultiple($table, $data);
            }
        }
        return $this;
    }

    /**
     * Save Shipping Store
     */
    protected function _saveShippingStores($rule)
    {
        $oldStores = $this->lookupStoreIds($rule->getId());
        $newStores = (array)$rule->getStores();
        if (empty($newStores)) {
            $newStores = (array)$rule->getStoreId();
        }
        $table = $this->getTable('royalcybermarketplace_shippingfilter_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = ['rule_id = ?' => (int)$rule->getId(), 'store_id IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['rule_id' => (int)$rule->getId(), 'store_id' => (int)$storeId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
        return $this;
    }
    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $ruleId
     * @return array
     */
    protected function lookupStoreIds($ruleId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('royalcybermarketplace_shippingfilter_store'),
            'store_id'
        )
            ->where(
                'rule_id = ?',
                (int)$ruleId
            );
        return $connection->fetchCol($select);
    }
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $customerGroups = $this->lookupCustomerGroupIds($object->getId());
            $object->setData('stores', $stores);
            $object->setData('customer_groups', $customerGroups);
            if ($shippingMethods = $object->getData('available_shipments')) {
                $object->setData('available_shipments', explode(',', $shippingMethods));
            }
            if ($location = $object->getData('apply_area')) {
                $object->setData('apply_area', explode(',', $location));
            }
        }
        return parent::_afterLoad($object);
    }
    public function lookupCustomerGroupIds($ruleId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('royalcybermarketplace_shippingfilter_customer_group'),
            'customer_group_id'
        )
            ->where(
                'rule_id = ?',
                (int)$ruleId
            );
        return $connection->fetchCol($select);
    }
    /**
     * Process brand data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['rule_id = ?' => (int)$object->getId()];
        $getStTable = $this->getTable('royalcybermarketplace_shippingfilter_store');
        $getCSTable = $this->getTable('royalcybermarketplace_shippingfilter_customer_group');
        $this->getConnection()->delete($getStTable, $condition);
        $this->getConnection()->delete($getStTable, $condition);
        return parent::_beforeDelete($object);
    }
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('royalcybermarketplace_shippingfilter'),
        )
            ->where(
                'rule_name = ?',
                $object->getData('rule_name')
            );
        $exist = $connection->fetchCol($select);
        if ((count($exist) > 0) && !in_array($object->getData('rule_id'), $exist)) {
            throw new LocalizedException(
                __('A rule with the same name already exists.')
            );
        }
        return $this;
    }
}
