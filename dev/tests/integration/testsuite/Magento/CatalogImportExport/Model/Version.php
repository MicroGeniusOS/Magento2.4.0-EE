<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogImportExport\Model;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class responsible for creating schedule update for products.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Version
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create update for product
     *
     * @param array $skus
     * @param AbstractProductExportImportTestCase $testInstance
     * @return void
     * @throws \Exception
     */
    public function create(array $skus, AbstractProductExportImportTestCase $testInstance = null): void
    {
        $date = new \DateTime();
        /** @var \Magento\Staging\Model\UpdateFactory $updateFactory */
        $updateFactory = $this->objectManager->get(\Magento\Staging\Model\UpdateFactory::class);
        /** @var \Magento\Framework\EntityManager\MetadataPool $metadataPool */
        $metadataPool = $this->objectManager->get(\Magento\Framework\EntityManager\MetadataPool::class);
        /** @var \Magento\Catalog\Model\ResourceModel\Product $productResource */
        $productResource = $this->objectManager->get(\Magento\Catalog\Model\ResourceModel\Product::class);
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->objectManager->create(\Magento\Catalog\Model\Product::class);
        /** @var \Magento\Staging\Model\ResourceModel\Update $resourceUpdate */
        $resourceUpdate = $this->objectManager->get(\Magento\Staging\Model\ResourceModel\Update::class);
        /** @var \Magento\Framework\EntityManager\EntityManager $entityManager */
        $entityManager = $this->objectManager->get(\Magento\Framework\EntityManager\EntityManager::class);
        /** @var \Magento\Staging\Model\VersionManager $versionManager */
        $versionManager = $this->objectManager->get(\Magento\Staging\Model\VersionManager::class);
        /** @var \Magento\Framework\EntityManager\HydratorInterface $hydrator */
        $hydrator = $metadataPool->getHydrator(\Magento\Staging\Api\Data\UpdateInterface::class);

        $i = 2;
        foreach ($skus as $sku) {
            $startDate = $date->add(new \DateInterval('P' . $i . 'D'))->format('Y-m-d H:i:s');

            $productId = $productResource->getIdBySku($sku);
            $product->clearInstance()->load($productId);

            $stagingData = [
                'mode'        => 'save',
                'update_id'   => null,
                'name'        => 'New update ' . $startDate,
                'description' => 'New update',
                'start_time'  => $startDate,
                'end_time'    => null,
                'select_id'   => null,
            ];

            /** @var \Magento\Staging\Model\Update $update */
            $update = $updateFactory->create();
            $hydrator->hydrate($update, $stagingData);
            $update->setIsCampaign(false);
            $update->setId(strtotime($update->getStartTime()));
            $update->isObjectNew(true);

            $resourceUpdate->save($update);

            $oldVersion = $versionManager->getCurrentVersion();
            $versionManager->setCurrentVersionId($update->getId());

            $this->prepareCustomOptions($product);
            if ($testInstance) {
                $testInstance->prepareProduct($product);
            }

            $product->unsRowId();
            $product->setName('My Product ' . $startDate);
            $entityManager->save($product, ['created_in' => $update->getId()]);
            $versionManager->setCurrentVersionId($oldVersion->getId());

            $i++;
        }
    }

    /**
     * Prepare custom options
     *
     * @param \Magento\Catalog\Model\Product $product
     */
    public function prepareCustomOptions($product)
    {
        $this->objectManager->removeSharedInstance(\Magento\Catalog\Model\ProductRepository::class);
        $this->objectManager->removeSharedInstance(\Magento\Catalog\Model\Product\Option\Repository::class);
        $this->objectManager->removeSharedInstance(\Magento\Catalog\Model\Product\Option\SaveHandler::class);

        if ($product->getOptions()) {
            /** @var \Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory $customOptionFactory */
            $customOptionFactory = $this->objectManager->get(
                \Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory::class
            );

            $options = [];
            foreach ($product->getOptions() as $option) {
                $optionData = $option->getData();
                unset(
                    $optionData['id'],
                    $optionData['option_id'],
                    $optionData['product_id']
                );
                $optionValues = [];
                if ($option->getValues()) {
                    foreach ($option->getValues() as $optionValue) {
                        $optionValueData = $optionValue->getData();
                        unset(
                            $optionValueData['option_type_id'],
                            $optionValueData['option_id']
                        );
                        $optionValues[] = $optionValueData;
                    }
                    $optionData['values'] = $optionValues;
                }
                /** @var \Magento\Catalog\Api\Data\ProductCustomOptionInterface $option */
                $option = $customOptionFactory->create(['data' => $optionData]);
                $option->setProductSku($product->getSku());
                $options[] = $option;
            }
            $product->setOptions($options);
        }
    }
}
