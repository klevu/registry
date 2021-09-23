<?php

namespace Klevu\Registry\Test\Integration\Controller;

use Klevu\Registry\Api\CategoryRegistryInterface;
use Klevu\Registry\Api\ProductRegistryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController as AbstractControllerTestCase;

class ProductPageTest extends AbstractControllerTestCase
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
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var string
     */
    private $urlSuffix;

    /**
     * @magentoAppArea frontend
     * @magentoCache all disabled
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture loadProductFixtures
     */
    public function testRegistryValuesOnEnabledProduct()
    {
        $this->setupPhp5();

        /** @var ProductRegistryInterface $productRegistry */
        $productRegistry = $this->objectManager->get(ProductRegistryInterface::class);
        /** @var CategoryRegistryInterface $categoryRegistry */
        $categoryRegistry = $this->objectManager->get(CategoryRegistryInterface::class);

        $product = $this->productRepository->get('klevu_simple_1');
        $url = $this->prepareUrl($product->getUrlKey());

        $this->dispatch($url);

        $response = $this->getResponse();
        $this->assertSame(200, $response->getHttpResponseCode());

        $currentCategory = $categoryRegistry->getCurrentCategory();
        $this->assertNull($currentCategory);

        $currentProduct = $productRegistry->getCurrentProduct();
        $this->assertInstanceOf(ProductInterface::class, $currentProduct);
        $this->assertSame('klevu_simple_1', $currentProduct->getSku());
    }

    /**
     * @magentoAppArea frontend
     * @magentoCache all disabled
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture loadProductFixtures
     */
    public function testRegistryValuesOn404NotFound()
    {
        $this->setupPhp5();

        /** @var ProductRegistryInterface $productRegistry */
        $productRegistry = $this->objectManager->get(ProductRegistryInterface::class);
        /** @var CategoryRegistryInterface $categoryRegistry */
        $categoryRegistry = $this->objectManager->get(CategoryRegistryInterface::class);

        $this->dispatch('catalog/product/view/id/999999');

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
     * @magentoDbIsolation disabled
     * @magentoDataFixture loadProductFixtures
     */
    public function testRegistryValuesOnDisabledProduct()
    {
        $this->setupPhp5();

        /** @var ProductRegistryInterface $productRegistry */
        $productRegistry = $this->objectManager->get(ProductRegistryInterface::class);
        /** @var CategoryRegistryInterface $categoryRegistry */
        $categoryRegistry = $this->objectManager->get(CategoryRegistryInterface::class);

        $product = $this->productRepository->get('klevu_simple_2');
        $url = $this->prepareUrl($product->getUrlKey());

        $this->dispatch($url);

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
        $this->productRepository = $this->objectManager->get(ProductRepositoryInterface::class);
        $this->urlSuffix = $this->scopeConfig->getValue(
            ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX,
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
     * Loads product creation scripts because annotations use a relative path
     *  from integration tests root
     */
    public static function loadProductFixtures()
    {
        include __DIR__ . '/../_files/productFixtures.php';
    }

    /**
     * Rolls back product creation scripts because annotations use a relative path
     *  from integration tests root
     */
    public static function loadProductFixturesRollback()
    {
        include __DIR__ . '/../_files/productFixtures_rollback.php';
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
