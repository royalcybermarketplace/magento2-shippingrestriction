<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

namespace RoyalCyberMarketplace\ShippingRestriction\Api\Data;

/**
 * Interface ShippingFilterInterface
 */
interface ShippingFilterInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**
     * @return int
     */
    public function getRuleId();

    /**
     * @param int $ruleId
     * @return this
     */
    public function setRuleId($ruleId);

    /**
     * @return string
     */
    public function getRuleName();

    /**
     * @param string $rule_name
     * @return this
     */
    public function setRuleName($rule_name);

    /**
     * @return string
     */
    public function getApplyArea();

    /**
     * @param string $apply_area
     * @return this
     */
    public function setApplyArea($apply_area);

    /**
     * @return string
     */
    public function getRuleStatus();

    /**
     * @param string $rule_status
     * @return this
     */
    public function setRuleStatus($rule_status);

    /**
     * @return int
     */
    public function getTimeFrom();

    /**
     * @param int $time_from
     * @return this
     */
    public function setTimeFrom($time_from);

    /**
     * @return int
     */
    public function getTimeTo();

    /**
     * @param int $time_to
     * @return this
     */
    public function setTimeTo($time_to);

    /**
     * @return mixed
     */
    public function getCustomerGroupId();

    /**
     * @param string[] $customerGroupId
     * @return this
     */
    public function setCustomerGroupId($customerGroupId);

    /**
     * @return string[]
     */
    public function getAvailableShipments();

    /**
     * @param string[] $available_shipments
     * @return this
     */
    public function setAvailableShipments(array $available_shipments);

    /**
     * @return mixed
     */
    public function getStores();

    /**
     * @param mixed $stores
     * @return this
     */
    public function setStores($stores);

    /**
     * @return string
     */
    public function getConditionsSerialized();

    /**
     * @param string $conditions_serialized
     * @return this
     */
    public function setConditionsSerialized($conditions_serialized);

    /**
     * @return mixed
     */
    public function getConditions();

    /**
     * @param mixed $conditions
     * @return this
     */
    public function setConditions($conditions);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $creation_time
     * @return this
     */
    public function setCreatedAt($creation_time);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $update_time
     * @return this
     */
    public function setUpdatedAt($update_time);

    /**
     * @param $entityType
     * @return mixed
     */
    public function setEntityType($entityType);

    /**
     * @return mixed
     */
    public function getEntityType();
}
