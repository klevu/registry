<?php

namespace Klevu\Registry\Observer;

use Klevu\Registry\Api\ProductRegistryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RegisterCurrentProductObserver implements ObserverInterface
{
    /**
     * @var ProductRegistryInterface
     */
    private $productRegistry;

    /**
     * RegisterCurrentProductObserver constructor.
     * @param ProductRegistryInterface $productRegistry
     */
    public function __construct(ProductRegistryInterface $productRegistry)
    {
        $this->productRegistry = $productRegistry;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getDataUsingMethod('product');
        if (!($product instanceof ProductInterface)) {
            return;
        }

        $this->productRegistry->setCurrentProduct($product);
    }
}
