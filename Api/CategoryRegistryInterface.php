<?php

namespace Klevu\Registry\Api;

use Magento\Catalog\Api\Data\CategoryInterface;

interface CategoryRegistryInterface
{
    /**
     * @param CategoryInterface $currentCategory
     * @return void
     */
    public function setCurrentCategory(CategoryInterface $currentCategory);

    /**
     * @return CategoryInterface|null
     */
    public function getCurrentCategory();
}
