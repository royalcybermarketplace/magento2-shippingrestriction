<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */
 
namespace RoyalCyberMarketplace\ShippingRestriction\Block\Adminhtml\Shippingfilter\Edit;

use RoyalCyberMarketplace\ShippingRestriction\Block\Adminhtml\Shippingfilter\Edit\Tab\General;
use RoyalCyberMarketplace\ShippingRestriction\Block\Adminhtml\Shippingfilter\Edit\Tab\Conditions;
use RoyalCyberMarketplace\ShippingRestriction\Block\Adminhtml\Shippingfilter\Edit\Tab\Actions;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rule_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Rule Information'));

        $this->addTab(
            'general',
            [
                'label' => __('General'),
                'content' => $this->getLayout()->createBlock(General::class)->toHtml()
            ]
        )->addTab(
            'conditions',
            [
                'label' => __('Conditions'),
                'content' => $this->getLayout()->createBlock(Conditions::class)->toHtml()
            ]
        )->addTab(
            'actions',
            [
                'label' => __('Actions'),
                'content' => $this->getLayout()->createBlock(Actions::class)->toHtml()
            ]
        );
    }
}
