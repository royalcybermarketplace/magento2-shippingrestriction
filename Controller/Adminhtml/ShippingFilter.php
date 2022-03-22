<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

declare(strict_types=1);

namespace RoyalCyberMarketplace\ShippingRestriction\Controller\Adminhtml;

abstract class ShippingFilter extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'RoyalCyberMarketplace_ShippingRestriction::top_level';

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('RoyalCyberMarketplace'), __('RoyalCyberMarketplace'))
            ->addBreadcrumb(__('Shippingrestriction'), __('Shippingrestriction'));
        return $resultPage;
    }
}
