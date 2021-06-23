<?php

namespace Klevu\Registry\Observer;

use Klevu\Registry\Api\CategoryRegistryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RegisterCurrentCategoryObserver implements ObserverInterface
{
    /**
     * @var CategoryRegistryInterface
     */
    private $categoryRegistry;

    /**
     * RegisterCurrentCategoryObserver constructor.
     * @param CategoryRegistryInterface $categoryRegistry
     */
    public function __construct(CategoryRegistryInterface $categoryRegistry)
    {
        $this->categoryRegistry = $categoryRegistry;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $category = $observer->getDataUsingMethod('category');
        if (!($category instanceof CategoryInterface)) {
            return;
        }

        $this->categoryRegistry->setCurrentCategory($category);
    }
}
