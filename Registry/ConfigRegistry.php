<?php

namespace Klevu\Registry\Registry;

use Klevu\Registry\Api\ConfigRegistryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Sometimes we need to retrieve a config value, reinit the config (clearing
 *  its internal cache) and then reference the same config value
 * By using a registry, we avoid undoing the previous reinit config or having
 *  to repeatedly clear the config cache indiscriminately
 */
class ConfigRegistry implements ConfigRegistryInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var bool
     */
    private $isSingleStoreMode;

    /**
     * @var array[]
     */
    private $configCache = [
        'value' => [],
        'is_set_flag' => [],
    ];

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->isSingleStoreMode = null;
        array_walk($this->configCache, static function (&$value) {
            $value = [];
        });
    }

    /**
     * @return bool
     */
    public function isSingleStoreMode()
    {
        if (null === $this->isSingleStoreMode) {
            $this->isSingleStoreMode = $this->storeManager->isSingleStoreMode();
        }

        return $this->isSingleStoreMode;
    }

    /**
     * @param string $configPath
     * @param string $scopeType
     * @param int $scopeId
     * @return mixed
     */
    public function getValue($configPath, $scopeType, $scopeId)
    {
        $cacheKey = $this->getCacheKey($configPath, $scopeType, $scopeId);
        if (!array_key_exists($cacheKey, $this->configCache['value'])) {
            $this->configCache['value'][$cacheKey] = $this->scopeConfig->getValue(
                $configPath,
                $scopeType,
                $scopeId
            );
        }

        return $this->configCache['value'][$cacheKey];
    }

    /**
     * @param string $configPath
     * @param string $scopeType
     * @param int $scopeId
     * @return bool
     */
    public function isSetFlag($configPath, $scopeType, $scopeId)
    {
        $cacheKey = $this->getCacheKey($configPath, $scopeType, $scopeId);
        if (!array_key_exists($cacheKey, $this->configCache['is_set_flag'])) {
            $this->configCache['is_set_flag'][$cacheKey] = $this->scopeConfig->isSetFlag(
                $configPath,
                $scopeType,
                $scopeId
            );
        }

        return $this->configCache['is_set_flag'][$cacheKey];
    }

    /**
     * @param string $configPath
     * @param string $scopeType
     * @param int $scopeId
     * @return string
     */
    private function getCacheKey($configPath, $scopeType, $scopeId)
    {
        return (string)$configPath
            . ':' . (string)$scopeType
            . ':' . (int)$scopeId;
    }
}
