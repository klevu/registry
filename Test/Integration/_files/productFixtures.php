<?php

use Magento\Catalog\Model\Indexer\Product\Price\Processor as IndexerProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;

include __DIR__ . '/productFixtures_rollback.php';

$objectManager = Bootstrap::getObjectManager();

/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
$defaultStoreView = $storeManager->getDefaultStoreView();

$fixtures = [
    [
        'sku' => 'klevu_simple_1',
        'name' => '[Klevu] Simple Product 1',
        'description' => '[Klevu Test Fixtures] Simple product 1',
        'short_description' => '[Klevu Test Fixtures] Simple product 1',
        'attribute_set_id' => 4,
        'website_ids' => [
            $defaultStoreView->getWebsiteId(),
        ],
        'price' => 10,
        'weight' => 1,
        'tax_class_id' => 2,
        'meta_title' => '[Klevu] Simple Product 1',
        'meta_description' => '[Klevu Test Fixtures] Simple product 1',
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_ENABLED,
        'stock_data' => [
            'use_config_manage_stock'   => 1,
            'qty'                       => 100,
            'is_qty_decimal'            => 0,
            'is_in_stock'               => 1,
        ],
        'url_key' => 'klevu-simple-product-1',
    ], [
        'sku' => 'klevu_simple_2',
        'name' => '[Klevu] Simple Product 2',
        'description' => '[Klevu Test Fixtures] Simple product 2',
        'short_description' => '[Klevu Test Fixtures] Simple product 2',
        'attribute_set_id' => 4,
        'website_ids' => [
            $defaultStoreView->getWebsiteId(),
        ],
        'price' => 10,
        'weight' => 1,
        'tax_class_id' => 2,
        'meta_title' => '[Klevu] Simple Product 2',
        'meta_description' => '[Klevu Test Fixtures] Simple product 2',
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_DISABLED,
        'stock_data' => [
            'use_config_manage_stock'   => 1,
            'qty'                       => 100,
            'is_qty_decimal'            => 0,
            'is_in_stock'               => 1,
        ],
        'url_key' => 'klevu-simple-product-2',
    ]
];

$indexerProcessor = $objectManager->get(IndexerProcessor::class);
foreach ($fixtures as $fixture) {
    /** @var $product Product */
    $product = $objectManager->create(Product::class);
    $product->isObjectNew(true);
    $product->addData($fixture);

    $product = $product->save();
    $indexerProcessor->reindexRow($product->getId());
}
