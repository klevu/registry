<?php

namespace Klevu\Registry\Api;

interface ConfigRegistryInterface
{
    /**
     * @return void
     */
    public function reset();

    /**
     * @return bool
     */
    public function isSingleStoreMode();

    /**
     * @param string $configPath
     * @param string $scopeType
     * @param int $scopeId
     * @return mixed
     */
    public function getValue($configPath, $scopeType, $scopeId);

    /**
     * @param string $configPath
     * @param string $scopeType
     * @param int $scopeId
     * @return bool
     */
    public function isSetFlag($configPath, $scopeType, $scopeId);
}
