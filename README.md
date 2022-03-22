# Magento 2 ShippingRestriction


## 1.Installation instruction:

##### Using Composer
```
composer require royalcybermarketplace/magento2-shipping-restriction

```

### 1.1 - Enable And Install the extension
 * php bin/magento module:enable RoyalCyberMarketplace_ShippingRestriction
 * php bin/magento setup:upgrade
 * php bin/magento setup:di:compile
 * php bin/magento cache:clean

### 1.2 - How to see the results
- Going to the checkout page on the front end.Enter the address and check the shipping methods
- The administrator go to Sales ->Shipping Restriction ->Manage Rule
- Store -> Configuration -> RoyalCyberMarketplace -> Shipping Restriction to see the configurations of extension.