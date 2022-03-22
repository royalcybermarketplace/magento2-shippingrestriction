<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

namespace RoyalCyberMarketplace\ShippingRestriction\Model\Entity\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Helper\Data;
use Magento\Shipping\Model\Config;
use Magento\Store\Model\StoreManagerInterface;

class Options extends AbstractSource
{
    /**
     * @var Data
     */
    protected $paymentData;
    /**
     * @var Config
     */
    private $shippingData;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor
     *
     * @param Data $paymentData
     */
    public function __construct(
        Data $paymentData,
        StoreManagerInterface $storeManager,
        Config $shippingConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->shippingData = $shippingConfig;
        $this->paymentData = $paymentData;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getShippingOptions()
    {
        return $this->getAllOptions();
    }
    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getLocationOptions()
    {
        return [
            [
                'value' => 1,
                'label' => __('Backend Order')
            ],
            [
                'value' => 0,
                'label' => __('Frontend Order')
            ],
        ];
    }
    public function getActionOptions()
    {
        return [
            [
                'value' => 1,
                'label' => __('Show')
            ],
            [
                'value' => 0,
                'label' => __('Hide')
            ],
        ];
    }

    public function getAllOptions()
    {
        $activeCarriers = $this->shippingData->getAllCarriers();
        $shippingmethods = [];
        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            $options = [];
            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                    $code = $carrierCode.'_'.$methodCode;
                    $options[]= ['value'=>$code,'label'=>$method];
                }
                 $carrierTitle = $this->scopeConfig->getValue('carriers/'.$carrierCode.'/title');
            }
            $shippingmethods[]=['value'=>$options,'label'=>$carrierTitle];
        }

        return $shippingmethods;
    }
}
