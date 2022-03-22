<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

namespace RoyalCyberMarketplace\ShippingRestriction\Block\Adminhtml\Shippingfilter\Edit\Tab;

use RoyalCyberMarketplace\ShippingRestriction\Model\Entity\Attribute\Source\Options;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Class Actions
 */
class Actions extends Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var Config
     */
    protected $_wysiwygConfig;
    
    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;
    
    /**
     * @var DataObject
     */
    protected $_objectConverter;
    
    /**
     * @var DataObject
     */
    protected $SearchCriteriaBuilder;

    /**
     * @var Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var FieldFactory
     */
    protected $_fieldFactory;
    /**
     * @var Options
     */
    private $options;

    /**
     * @param Context
     * @param Registry
     * @param FormFactory
     * @param Config
     * @param GroupRepositoryInterface
     * @param DataObject
     * @param array
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        GroupRepositoryInterface $groupRepository,
        DataObject $dataobject,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Fieldset $rendererFieldset,
        Options $options,
        FieldFactory $fieldFactory,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->groupRepository = $groupRepository;
        $this->_objectConverter = $dataobject;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_rendererFieldset = $rendererFieldset;
        $this->options = $options;
        $this->_fieldFactory = $fieldFactory;

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

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('general');

        $fieldset = $form->addFieldset('actions', ['legend' => __('Actions'), 'collapsable' => false]);
        $fieldset->addField('available_shipments', 'multiselect', [
            'name' => 'available_shipments',
            'label' => __('Select Shipping Method(s)'),
            'title' => __('Select Shipping Method(s)'),
            'values' => $this->options->getShippingOptions()
        ]);
        $fieldset->addField('action', 'select', [
            'name' => 'action',
            'label' => __('Action'),
            'title' => __('Action'),
            'values' => $this->options->getActionOptions()
        ]);
        $fieldset->addField('apply_area', 'multiselect', [
            'name' => 'apply_area',
            'label' => __('Location'),
            'title' => __('Location'),
            'values' => $this->options->getLocationOptions()
        ]);

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
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General');
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
