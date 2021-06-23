<?php

namespace Klevu\Registry\Api;

use Magento\Catalog\Api\Data\ProductInterface;

interface ProductRegistryInterface
{
    /**
     * @param ProductInterface $currentProduct
     * @return void
     */
    public function setCurrentProduct(ProductInterface $currentProduct);

    /**
     * @return ProductInterface|null
     */
    public function getCurrentProduct();
}
