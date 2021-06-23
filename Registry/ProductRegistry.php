<?php

namespace Klevu\Registry\Registry;

use Klevu\Registry\Api\ProductRegistryInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class ProductRegistry implements ProductRegistryInterface
{
    /**
     * @var ProductInterface
     */
    private $currentProduct;

    /**
     * @param ProductInterface $currentProduct
     */
    public function setCurrentProduct(ProductInterface $currentProduct)
    {
        $this->currentProduct = $currentProduct;
    }

    /**
     * @return ProductInterface|null
     */
    public function getCurrentProduct()
    {
        return $this->currentProduct;
    }
}
