<?php

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\TestFramework\Helper\Bootstrap;

include __DIR__ . '/categoryFixtures_rollback.php';

$objectManager = Bootstrap::getObjectManager();

/** @var CategoryRepositoryInterface $categoryRepository */
$categoryRepository = $objectManager->get(CategoryRepositoryInterface::class);

$fixtures = [
    [
        'name' => '[Klevu] Parent Category 1',
        'description' => '[Klevu Test Fixtures] Parent category 1',
        'parent_id' => 2,
        'path' => '1/2',
        'level' => 2,
        'available_sort_by' => 'name',
        'default_sort_by' => 'name',
        'is_active' => true,
        'position' => 1001,
        'url_key' => 'klevu-test-category-1',
    ], [
        'name' => '[Klevu] Parent Category 2',
        'description' => '[Klevu Test Fixtures] Parent category 2',
        'parent_id' => 2,
        'path' => '1/2',
        'level' => 2,
        'available_sort_by' => 'name',
        'default_sort_by' => 'name',
        'is_active' => false,
        'position' => 1002,
        'url_key' => 'klevu-test-category-2',
    ]
];

foreach ($fixtures as $fixture) {
    /** @var Category $category */
    $category = $objectManager->create(Category::class);
    $category->isObjectNew(true);
    $category->addData($fixture);

    $category = $categoryRepository->save($category);
}
