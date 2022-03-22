<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

namespace RoyalCyberMarketplace\ShippingRestriction\Api;

use RoyalCyberMarketplace\ShippingRestriction\Api\Data\ShippingFilterInterface;

/**
 * Interface ShippingFilterRepositoryInterface
 */
interface ShippingFilterRepositoryInterface
{
    /**
     * @param int $customerGroupId
     * @return ShippingFilterInterface
     */
    public function getByCustomerGroupId($customerGroupId);

    /**
     * @param int $ruleId
     * @param int $storeId
     * @return ShippingFilterInterface
     */
    public function getByRuleId($ruleId, $storeId);

    /**
     * @param int $ruleId
     * @return void
     */
    public function deleteById($ruleId);

    /**
     * @param ShippingFilterInterface $shippingFilter
     * @return ShippingFilterInterface
     */
    public function save(ShippingFilterInterface $shippingFilter);

    /**
     * @param ShippingFilterInterface $shippingFilter
     * @return void
     */
    public function delete(ShippingFilterInterface $shippingFilter);
}
