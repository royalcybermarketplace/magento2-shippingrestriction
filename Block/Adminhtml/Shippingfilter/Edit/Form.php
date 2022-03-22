<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

namespace RoyalCyberMarketplace\ShippingRestriction\Block\Adminhtml\Shippingfilter\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
            'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
