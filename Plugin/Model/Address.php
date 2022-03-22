<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */
 
namespace RoyalCyberMarketplace\ShippingRestriction\Plugin\Model;

use RoyalCyberMarketplace\ShippingRestriction\Model\ResourceModel\ShippingFilter\Collection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use RoyalCyberMarketplace\ShippingRestriction\Helper\Data;
use Magento\Framework\App\State as AppState;

/**
 * Class Address
 */
class Address
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var AppState
     */
    private $appState;
    
    /**
     * @param DateTime $date
     * @param Data $helper
     * @param Collection $collection
     * @param AppState $appState
     */
    public function __construct(
        DateTime $date,
        Data $helper,
        Collection $collection,
        AppState $appState
    ) {
        $this->date = $date;
        $this->helper = $helper;
        $this->collection = $collection;
        $this->appState = $appState;
    }

    public function afterGetGroupedAllShippingRates($address, $output)
    {
        $quote = $address->getQuote();
        return $this->filterOutput($quote, $output);
    }
    private function filterOutput($quote, $output)
    {
        if ($this->helper->getGeneralConfig('enabled') == 0) {
            return $output;
        }
        $methodList = $output;
        $date = strtotime($this->date->gmtDate());
        $state = $this->appState->getAreaCode();
        if ($state == 'adminhtml') {
            $state = '1';
        } else {
            $state = '0';
        }
        $storeId = $quote->getData('store_id');
        $customerGroupId = $quote->getData('customer_group_id');
        $ruleCollection = $this->collection->addFieldToFilter('rule_status', '1');
        foreach ($output as $key => $shippingMethod) {
            foreach ($shippingMethod as $keyItem => $item) {
                $shippingCode = $item->getCode();
                $sortOrder = 99999;
                foreach ($ruleCollection as $rule) {
                    $fromDate = strtotime($rule->getData('time_from'));
                    $toDate = strtotime($rule->getData('time_to'));
                    $StoresApply = $rule->getStoreId();
                    $customersGroupApply = $rule->getData('customer_group_id');
                    $shipmentApply = $rule->getData('shipping_methods');
                    $action = $rule->getData('action');
                    $rule->afterLoad();
                    if ($quote->isVirtual()) {
                        $address = $quote->getBillingAddress();
                    } else {
                        $address = $quote->getShippingAddress();
                    }
                    if ($shipmentApply['0'] && (!in_array($shippingCode, $shipmentApply))) {
                        continue;
                    }
                    if ($sortOrder < $rule->getData('priority')) {
                        continue;
                    }
                    if ($fromDate && ($fromDate > $date)) {
                        continue;
                    }
                    if ($toDate && ($toDate < $date)) {
                        continue;
                    }
                    if (strpos($rule->getApplyArea(), $state) === false) {
                        continue;
                    }
                    if (!in_array('0', $StoresApply) && !in_array($storeId, $StoresApply)) {
                        continue;
                    }
                    if ($customersGroupApply && (!in_array($customerGroupId, $customersGroupApply))) {
                        continue;
                    }
                    if (($rule->validate($address) && !$action) || (!$rule->validate($address) && $action)) {
                        unset($methodList[$key][$keyItem]);
                    } elseif (!in_array($shippingMethod, $methodList)) {
                        $methodList[] = $shippingMethod;
                    }
                    $sortOrder = $rule->getData('priority');
                }
            }
        }
        return $methodList;
    }
}
