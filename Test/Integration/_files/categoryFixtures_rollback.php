<?php

use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$pathsToDelete = [
    '1/2/3',
    '1/2/4',
];

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$collection = $objectManager->create(CategoryCollection::class);
$collection->addAttributeToFilter('path', ['in' => $pathsToDelete]);
$collection->load();
$collection->delete();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
