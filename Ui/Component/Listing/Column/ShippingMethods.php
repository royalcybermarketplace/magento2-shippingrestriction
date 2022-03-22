<?php
/**
 * @category    RoyalCyberMarketplace
 * @copyright   Copyright (c) 2022 RoyalCyberMarketplace (https://royalcyber.com/)
 */

namespace RoyalCyberMarketplace\ShippingRestriction\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use RoyalCyberMarketplace\ShippingRestriction\Model\ShippingFilterFactory;
use Magento\Shipping\Helper\Data;
use Magento\Shipping\Model\Config;

/**
 * Class ShippingMethods
 */
class ShippingMethods extends Column
{
    /**
     * @var Data
     */
    private $shippingData;
    /**
     * @var ShippingFilterFactory
     */
    private $shippingfilter;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ShippingFilterFactory $shippingFilter
     * @param Config $shippingConfig
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ShippingFilterFactory $shippingFilter,
        Config $shippingConfig,
        array $components = [],
        array $data = []
    ) {
        $this->shippingfilter = $shippingFilter;
        $this->shippingData = $shippingConfig;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    /**
     * @param array $items
     * @return array
     */
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $activeCarriers = $this->shippingData->getAllCarriers();
        $options = [];
        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                    $code = $carrierCode.'_'.$methodCode;
                    $options[$code]= ['value'=>$code,'label'=>$method];
                }
            }
        }
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['available_shipments'])) {
                    $shippings = explode(",", $item['available_shipments']);
                    $string = '';
                    foreach ($shippings as $k => $v) {
                        if (!isset($options[$v])) {
                            continue;
                        }
                        $shipping[$k] = $options[$v]['label'] . '</br>';
                        $string .= $shipping[$k];
                    }
                    $item[$this->getData('name')] = $string;
                }
            }
        }
        return $dataSource;
    }
}
