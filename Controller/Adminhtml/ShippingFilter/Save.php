<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

namespace RoyalCyberMarketplace\ShippingRestriction\Controller\Adminhtml\ShippingFilter;

use Magento\Framework\Exception\LocalizedException;
use RoyalCyberMarketplace\ShippingRestriction\Model\ShippingFilter;

/**
 * Class Save
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Stdlib\DateTime\Timezone $_stdTimezone,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_stdTimezone = $_stdTimezone;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $dateTimeNow = $this->_stdTimezone->date()->format('Y-m-d H:i:s');
        if ($data) {
            $id = $this->getRequest()->getParam('rule_id');
            $model = $this->_objectManager->create(ShippingFilter::class)->load($id);
            if ($id) {
                $data['updated_date'] = $dateTimeNow;
            } else {
                $data['created_date'] = $dateTimeNow;
                $data['updated_date'] = $dateTimeNow;
            }
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Rule no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            if (isset($data['apply_area']) && $data['apply_area']) {
                $data['apply_area'] = implode(",", $data['apply_area']);
            }
            if (isset($data['available_shipments']) && $data['available_shipments']) {
                $data['available_shipments'] = implode(",", $data['available_shipments']);
            }
            if (isset($data['rule'])) {
                $data = $this->prepareData($data);
            }
            $model->loadPost($data);
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Shippingrestriction.'));
                $this->dataPersistor->clear('royalcybermarketplace_shippingrestriction_shippingrestriction');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['rule_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Rule.'));
            }

            $this->dataPersistor->set('royalcybermarketplace_shippingrestriction_shippingrestriction', $data);
            return $resultRedirect->setPath('*/*/edit', ['rule_id' => $this->getRequest()->getParam('rule_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
    /**
     * Prepares specific data
     *
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        if (isset($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
        }
        unset($data['rule']);
        return $data;
    }
    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('RoyalCyberMarketplace_ShippingRestriction::save');
    }
}
