<?php

use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$skusToDelete = [
    'klevu_simple_1',
    'klevu_simple_2',
];

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
foreach ($skusToDelete as $sku) {
    try {
        $productRepository->delete(
            $productRepository->get($sku)
        );
    } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
        // this is fine
    }
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
