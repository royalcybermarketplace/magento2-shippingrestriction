<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

namespace RoyalCyberMarketplace\ShippingRestriction\Block\Adminhtml\Shippingfilter\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

class General extends Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    
    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;
    
    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_objectConverter;
    
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;
    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\Data\FormFactory
     * @param \Magento\Store\Model\System\Store
     * @param \Magento\Cms\Model\Wysiwyg\Config
     * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Convert\DataObject $dataobject,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->groupRepository = $groupRepository;
        $this->_objectConverter = $dataobject;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('shippingfilter_shippingfilter');

        if ($this->_isAllowedAction('RoyalCyberMarketplace_ShippingRestriction::shippingfilter')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('actions');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Rule Information')]);

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        } else {
            $model->setData('stores', '[0]');
        }
        $fieldset->addField('rule_name', 'text', [
            'name'     => 'rule_name',
            'label'    => __('Rule Name'),
            'title'    => __('Rule Name '),
            'required' => true
        ]);
        $fieldset->addField('rule_status', 'select', [
            'name'     => 'rule_status',
            'label'    => __('Status'),
            'title'    => __('Status'),
            'values'   => $model->toOptionArray()
        ]);
        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField('stores', 'hidden', [
                'name' => 'stores[]',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
            ]);
        } else {
            $fieldset->addField('stores', 'multiselect', [
                'name' => 'stores[]',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, true),
            ]);
        }

        $groups = $this->groupRepository
            ->getList($this->_searchCriteriaBuilder->create())
            ->getItems();
        $fieldset->addField('customer_groups', 'multiselect', [
            'name' => 'customer_groups[]',
            'label' => __('Customer Groups'),
            'title' => __('Customer Groups'),
            'requires' => false,
            'values' => $this->_objectConverter->toOptionArray($groups, 'id', 'code'),
            'note' => __('Leave empty or select all to apply the rule to any group'),
        ]);
        $fieldset->addField('time_from', 'date', [
            'name'        => 'time_from',
            'label'       => __('Active From Date'),
            'title'       => __('Active From Date'),
            'date_format' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
            'class'       => 'validate-date',
        ]);
        $fieldset->addField('time_to', 'date', [
            'name'        => 'time_to',
            'label'       => __('Active To Date'),
            'title'       => __('Active To Date'),
            'date_format' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
            'class'       => 'validate-date',
        ]);

        $fieldset->addField('priority', 'text', [
            'name'        => 'priority',
            'label'       => __('Priority'),
            'title'       => __('Priority'),
            'class'       => 'validate-digits',
            'note'        => __('Default is 0. The promotion bar with the lower number will get the higher priority.'),
        ]);

        if (!$model->getId()) {
            $model->setData('rule_status', $isElementDisabled ? '0' : '1');
            $model->setData('stores', '0');
        }

        $this->_eventManager->dispatch('shippingfilter_shippingfilter_edit_tab_detail_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Rule Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
