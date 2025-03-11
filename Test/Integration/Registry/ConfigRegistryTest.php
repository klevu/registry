<?php

namespace Klevu\Registry\Test\Integration\Registry;

use Klevu\Registry\Api\ConfigRegistryInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface as ConfigWriterInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class ConfigRegistryTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoConfigFixture default/general/single_store_mode/enabled 1
     * @magentoConfigFixture default_store general/single_store_mode/enabled 1
     */
    public function testIsSingleStoreModeEnabled()
    {
        $this->setupPhp5();

        /** @var StoreManagerInterface $storeManager */
        $storeManager = $this->objectManager->get(StoreManagerInterface::class);
        $this->assertTrue($storeManager->isSingleStoreMode(), 'Store Manager');

        /** @var ConfigRegistryInterface $configRegistry */
        $configRegistry = $this->objectManager->get(ConfigRegistryInterface::class);

        $this->assertTrue($configRegistry->isSingleStoreMode(), 'Config Registry');
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoConfigFixture default/general/single_store_mode/enabled 0
     * @magentoConfigFixture default_store general/single_store_mode/enabled 0
     */
    public function testIsSingleStoreModeDisabled()
    {
        $this->setupPhp5();

        /** @var StoreManagerInterface $storeManager */
        $storeManager = $this->objectManager->get(StoreManagerInterface::class);
        $this->assertFalse($storeManager->isSingleStoreMode(), 'Store Manager');

        /** @var ConfigRegistryInterface $configRegistry */
        $configRegistry = $this->objectManager->get(ConfigRegistryInterface::class);

        $this->assertFalse($configRegistry->isSingleStoreMode(), 'Config Registry');
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoConfigFixture default/general/single_store_mode/enabled 0
     * @magentoConfigFixture default_store general/single_store_mode/enabled 0
     */
    public function testIsSingleStoreModeUsesInternalCache()
    {
        $this->setupPhp5();

        /** @var StoreManagerInterface $storeManager */
        $storeManager = $this->objectManager->get(StoreManagerInterface::class);
        $this->assertFalse($storeManager->isSingleStoreMode(), 'Store Manager pre-update');

        /** @var ConfigRegistryInterface $configRegistry */
        $configRegistry = $this->objectManager->get(ConfigRegistryInterface::class);

        $this->assertFalse($configRegistry->isSingleStoreMode(), 'Registry pre-update');

        /** @var ConfigWriterInterface $configWriter */
        $configWriter = $this->objectManager->get(ConfigWriterInterface::class);
        $configWriter->save(
            'general/single_store_mode/enabled',
            1,
            'default',
            0
        );
        $configWriter->save(
            'general/single_store_mode/enabled',
            1,
            'stores',
            1
        );

        /** @var ReinitableConfigInterface $reinitableConfig */
        $reinitableConfig = $this->objectManager->get(ReinitableConfigInterface::class);
        $reinitableConfig->reinit();

        $this->assertTrue($storeManager->isSingleStoreMode(), 'Store Manager post-update');
        $this->assertFalse($configRegistry->isSingleStoreMode(), 'Registry post-update');
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoConfigFixture default/klevu_search/foo/bar abcde
     * @magentoConfigFixture default_store klevu_search/foo/bar 12345
     */
    public function testGetValue()
    {
        $this->setupPhp5();

        /** @var ConfigRegistryInterface $configRegistry */
        $configRegistry = $this->objectManager->get(ConfigRegistryInterface::class);

        $this->assertSame(
            'abcde',
            $configRegistry->getValue('klevu_search/foo/bar', 'default', 0),
            'Registry (Global) pre-update'
        );
        $this->assertSame(
            '12345',
            $configRegistry->getValue('klevu_search/foo/bar', 'stores', 1),
            'Registry (Store scope) pre-update'
        );

        /** @var ConfigWriterInterface $configWriter */
        $configWriter = $this->objectManager->get(ConfigWriterInterface::class);
        $configWriter->save(
            'klevu_search/foo/bar',
            'zyxwv',
            'default',
            0
        );
        $configWriter->save(
            'klevu_search/foo/bar',
            98765,
            'stores',
            1
        );

        /** @var ReinitableConfigInterface $reinitableConfig */
        $reinitableConfig = $this->objectManager->get(ReinitableConfigInterface::class);
        $reinitableConfig->reinit();

        /** @var ScopeConfigInterface $scopeConfig */
        $scopeConfig = $this->objectManager->get(ScopeConfigInterface::class);

        $this->assertSame(
            'zyxwv',
            $scopeConfig->getValue('klevu_search/foo/bar', 'default', 0),
            'Scope Config (Global) post-update'
        );
        $this->assertSame(
            'abcde',
            $configRegistry->getValue('klevu_search/foo/bar', 'default', 0),
            'Registry (Global) post-update'
        );
        $this->assertSame(
            '98765',
            $scopeConfig->getValue('klevu_search/foo/bar', 'stores', 1),
            'Scope Config (Store scope) post-update'
        );
        $this->assertSame(
            '12345',
            $configRegistry->getValue('klevu_search/foo/bar', 'stores', 1),
            'Registry (Store scope) post-update'
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoConfigFixture default/klevu_search/foo/bar 1
     * @magentoConfigFixture default_store klevu_search/foo/bar 0
     */
    public function testIsSetFlag()
    {
        $this->setupPhp5();

        /** @var ConfigRegistryInterface $configRegistry */
        $configRegistry = $this->objectManager->get(ConfigRegistryInterface::class);

        $this->assertTrue(
            $configRegistry->isSetFlag('klevu_search/foo/bar', 'default', 0),
            'Registry (Global) pre-update'
        );
        $this->assertFalse(
            $configRegistry->isSetFlag('klevu_search/foo/bar', 'stores', 1),
            'Registry (Store scope) pre-update'
        );

        /** @var ConfigWriterInterface $configWriter */
        $configWriter = $this->objectManager->get(ConfigWriterInterface::class);
        $configWriter->save(
            'klevu_search/foo/bar',
            '0',
            'default',
            0
        );
        $configWriter->save(
            'klevu_search/foo/bar',
            '1',
            'stores',
            1
        );

        /** @var ReinitableConfigInterface $reinitableConfig */
        $reinitableConfig = $this->objectManager->get(ReinitableConfigInterface::class);
        $reinitableConfig->reinit();

        /** @var ScopeConfigInterface $scopeConfig */
        $scopeConfig = $this->objectManager->get(ScopeConfigInterface::class);

        $this->assertFalse(
            $scopeConfig->isSetFlag('klevu_search/foo/bar', 'default', 0),
            'Scope Config (Global) post-update'
        );
        $this->assertTrue(
            $configRegistry->isSetFlag('klevu_search/foo/bar', 'default', 0),
            'Registry (Global) post-update'
        );
        $this->assertTrue(
            $scopeConfig->isSetFlag('klevu_search/foo/bar', 'stores', 1),
            'Scope Config (Store scope) post-update'
        );
        $this->assertFalse(
            $configRegistry->isSetFlag('klevu_search/foo/bar', 'stores', 1),
            'Registry (Store scope) post-update'
        );
    }

    /**
     * @return void
     * @todo Move to setUp when PHP 5.x is no longer supported
     */
    private function setupPhp5()
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
