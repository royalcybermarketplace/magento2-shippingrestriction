<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

declare(strict_types=1);

namespace RoyalCyberMarketplace\ShippingRestriction\Controller\Adminhtml\ShippingFilter;

use RoyalCyberMarketplace\ShippingRestriction\Model\ShippingFilterFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends \RoyalCyberMarketplace\ShippingRestriction\Controller\Adminhtml\ShippingFilter
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ShippingFilterFactory
     */
    private $shippingFilter;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ShippingFilterFactory $shippingFilter
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ShippingFilterFactory $shippingFilter,
        PageFactory $resultPageFactory
    ) {
        $this->shippingFilter = $shippingFilter;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('rule_id');
        $model = $this->shippingFilter->create();

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Rule no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('shippingfilter_shippingfilter', $model);

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Rule') : __('New Rule'),
            $id ? __('Edit Rule') : __('New Rule')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Shippingrestrictions'));
        $mId = $model->getId();
        $resultPage->getConfig()->getTitle()->prepend($mId ? __('Edit Rule %1', $mId) : __('New Rule'));
        return $resultPage;
    }
    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('RoyalCyberMarketplace_ShippingRestriction::edit');
    }
}
