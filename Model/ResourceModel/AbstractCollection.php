<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

namespace RoyalCyberMarketplace\ShippingRestriction\Model\ResourceModel;

use Magento\Store\Model\Store;

/**
 * Abstract collection of CMS pages and blocks
 */
abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        $this->metadataPool = $metadataPool;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function performAfterLoad($tableName, $linkField)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $getTableName = $this->getTable($tableName);
            $select = $connection->select()->from(['royalcybermarketplace_shippingfilter_store' => $getTableName])
                ->where($linkField . ' IN (?)', $linkedIds);
            $tbFrom = 'royalcybermarketplace_shippingfilter_customer_group';
            $getCSTableName = $this->getTable('royalcybermarketplace_shippingfilter_customer_group');
            $select2 = $connection->select()->from([$tbFrom => $getCSTableName])
                ->where($linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            $result2 = $connection->fetchAll($select2);
            $customersGroup = [];
            if ($result2) {
                foreach ($result2 as $customerGroup) {
                    $customersGroup[$customerGroup[$linkField]][] = $customerGroup['customer_group_id'];
                }
            }
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData[$linkField]][] = $storeData['store_id'];
                }
                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $storeIdKey = array_search(Store::DEFAULT_STORE_ID, $storesData[$linkedId], true);
                    if ($storeIdKey !== false) {
                        $stores = $this->storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = current($storesData[$linkedId]);
                        $storeCode = $this->storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', $storesData[$linkedId]);
                    if (isset($customersGroup[$linkedId]) && $customersGroup[$linkedId]) {
                        $item->setData('customer_group_id', $customersGroup[$linkedId]);
                    } else {
                        $item->setData('customer_group_id', []);
                    }
                    $shipment = explode(',', $item->getData('available_shipments'));
                    $item->setData('shipping_methods', $shipment);
                }
            }
        }
    }

    /**
     * Add field filter to collection
     *
     * @param array|string $field
     * @param string|int|array|null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add filter by store
     *
     * @param int|array|Store $store
     * @param bool $withAdmin
     * @return $this
     */
    abstract public function addStoreFilter($store, $withAdmin = true);

    /**
     * Perform adding filter by store
     *
     * @param int|array|Store $store
     * @param bool $withAdmin
     * @return void
     */
    protected function performAddStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store_id', ['in' => $store], 'public');
    }

    /**
     * Join store relation table if there is store filter
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function joinStoreRelationTable($tableName, $linkField)
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable($tableName)],
                'main_table.' . $linkField . ' = store_table.' . $linkField,
                []
            )->where('')->group(
                'main_table.' . $linkField
            );
        }

        parent::_renderFiltersBefore();
    }

    /**
     * @param int $group_id
     * @return $this
     */
    public function addCustomerGroupToFilter($group_id = 0)
    {
        if ($group_id) {
            $getCSTableName = $this->getTable('royalcybermarketplace_shippingfilter_customer_group');
            $this->getSelect()->join(
                ['royalcybermarketplace_shippingfilter_customer_group' => $getCSTableName],
                'main_table.rule_id = royalcybermarketplace_shippingfilter_customer_group.rule_id',
                []
            )->where('royalcybermarketplace_shippingfilter_customer_group.rule_id = ?', (int)$group_id)->group(
                'main_table.rule_id'
            );
        }
        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);

        return $countSelect;
    }
}
