<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */
 
namespace RoyalCyberMarketplace\ShippingRestriction\Controller\Adminhtml\ShippingFilter;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use RoyalCyberMarketplace\ShippingRestriction\Model\ShippingFilter;
use RoyalCyberMarketplace\ShippingRestriction\Controller\Adminhtml\ShippingFilter as ShpFilter;

class Delete extends ShpFilter implements HttpPostActionInterface
{
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('rule_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(ShippingFilter::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Rule.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['rule_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Rule to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('RoyalCyberMarketplace_ShippingRestriction::delete');
    }
}
