<?php

namespace Klevu\Registry\Registry;

use Klevu\Registry\Api\CategoryRegistryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;

class CategoryRegistry implements CategoryRegistryInterface
{
    /**
     * @var CategoryInterface
     */
    private $currentCategory;

    /**
     * @param CategoryInterface $currentCategory
     */
    public function setCurrentCategory(CategoryInterface $currentCategory)
    {
        $this->currentCategory = $currentCategory;
    }

    /**
     * @return CategoryInterface|null
     */
    public function getCurrentCategory()
    {
        return $this->currentCategory;
    }
}
