<?php

declare(strict_types=1);

namespace OnTap\MasterCard\Setup\Patch\Data;

use Exception;
use Magento\Config\Model\ResourceModel\Config\Data as ConfigResource;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch is mechanism, that allows to do atomic upgrade data changes
 */
class UninstallDirectMethod implements
    DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ConfigResource
     */
    private $configResource;

    /**
     * UninstallDirectMethod constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CollectionFactory $collectionFactory
     * @param ConfigResource $configResource
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CollectionFactory $collectionFactory,
        ConfigResource $configResource
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->collectionFactory = $collectionFactory;
        $this->configResource = $configResource;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function apply()
    {
        $this->removeConfigByPath('payment/tns_direct');
        $this->removeConfigByPath('payment/tns_direct_vault');
        return $this;
    }

    /**
     * @param string $path
     * @throws Exception
     */
    private function removeConfigByPath($path)
    {
        $configCollection = $this->collectionFactory->create();
        $configCollection->addPathFilter($path);
        /** @var AbstractModel $configItem */
        foreach ($configCollection->getItems() as $configItem) {
            $this->configResource->delete($configItem);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}
