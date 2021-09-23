<?php

namespace Klevu\Registry\Test\Integration\Controller;

use Klevu\Registry\Api\CategoryRegistryInterface;
use Klevu\Registry\Api\ProductRegistryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\TestFramework\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController as AbstractControllerTestCase;

class CategoryPageTest extends AbstractControllerTestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string
     */
    private $urlSuffix;

    /**
     * @magentoAppArea frontend
     * @magentoCache all disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testRegistryValuesOnEnabledCategory()
    {
        $this->setupPhp5();

        /** @var ProductRegistryInterface $productRegistry */
        $productRegistry = $this->objectManager->get(ProductRegistryInterface::class);
        /** @var CategoryRegistryInterface $categoryRegistry */
        $categoryRegistry = $this->objectManager->get(CategoryRegistryInterface::class);

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $this->assertSame(200, $response->getHttpResponseCode());

        $currentCategory = $categoryRegistry->getCurrentCategory();
        $this->assertInstanceOf(CategoryInterface::class, $currentCategory);
        $this->assertSame('[Klevu] Parent Category 1', $currentCategory->getName());

        $currentProduct = $productRegistry->getCurrentProduct();
        $this->assertNull($currentProduct);
    }

    /**
     * @magentoAppArea frontend
     * @magentoCache all disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testRegistryValuesOn404NotFound()
    {
        $this->setupPhp5();

        /** @var ProductRegistryInterface $productRegistry */
        $productRegistry = $this->objectManager->get(ProductRegistryInterface::class);
        /** @var CategoryRegistryInterface $categoryRegistry */
        $categoryRegistry = $this->objectManager->get(CategoryRegistryInterface::class);

        $this->dispatch('catalog/category/view/id/0');

        $this->assert404NotFound();

        $currentCategory = $categoryRegistry->getCurrentCategory();
        $this->assertNull($currentCategory);

        $currentProduct = $productRegistry->getCurrentProduct();
        $this->assertNull($currentProduct);
    }

    /**
     * @magentoAppArea frontend
     * @magentoCache all disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testRegistryValuesOnDisabledCategory()
    {
        $this->setupPhp5();

        /** @var ProductRegistryInterface $productRegistry */
        $productRegistry = $this->objectManager->get(ProductRegistryInterface::class);
        /** @var CategoryRegistryInterface $categoryRegistry */
        $categoryRegistry = $this->objectManager->get(CategoryRegistryInterface::class);

        $this->dispatch($this->prepareUrl('klevu-test-category-2'));

        $this->assert404NotFound();

        $currentCategory = $categoryRegistry->getCurrentCategory();
        $this->assertNull($currentCategory);

        $currentProduct = $productRegistry->getCurrentProduct();
        $this->assertNull($currentProduct);
    }

    /**
     * @return void
     * @todo Move to setUp when PHP 5.x is no longer supported
     */
    private function setupPhp5()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->scopeConfig = $this->objectManager->get(ScopeConfigInterface::class);
        $this->urlSuffix = $this->scopeConfig->getValue(
            CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Prepare url to dispatch
     *
     * @param string $urlKey
     * @param bool $addSuffix
     * @return string
     */
    private function prepareUrl($urlKey, $addSuffix = true)
    {
        return $addSuffix ? '/' . $urlKey . $this->urlSuffix : '/' . $urlKey;
    }

    /**
     * Loads category creation scripts because annotations use a relative path
     *  from integration tests root
     */
    public static function loadCategoryFixtures()
    {
        include __DIR__ . '/../_files/categoryFixtures.php';
    }

    /**
     * Rolls back category creation scripts because annotations use a relative path
     *  from integration tests root
     */
    public static function loadCategoryFixturesRollback()
    {
        include __DIR__ . '/../_files/categoryFixtures_rollback.php';
    }
}
