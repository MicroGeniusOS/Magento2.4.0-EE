<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ScheduledImportExport\Model;

use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoDbIsolation disabled
 */
class ImportTest extends \Magento\TestFramework\Indexer\TestCase
{
    public static function setUpBeforeClass(): void
    {
        $db = Bootstrap::getInstance()->getBootstrap()
            ->getApplication()
            ->getDbInstance();
        if (!$db->isDbDumpExists()) {
            throw new \LogicException('DB dump does not exist.');
        }
        $db->restoreFromDbDump();

        parent::setUpBeforeClass();
    }

    public function testRunSchedule()
    {
        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $productModel = $objectManager->create(\Magento\Catalog\Model\Product::class);
        $product = $productModel->loadByAttribute('sku', 'product_100500');
        // fixture
        $this->assertFalse($product);

        $model = $objectManager->create(
            \Magento\ScheduledImportExport\Model\Import::class,
            [
                'data' => [
                    'entity' => 'catalog_product',
                    'behavior' => 'append',
                ],
            ]
        );

        $operation = $objectManager->get(\Magento\ScheduledImportExport\Model\Scheduled\Operation::class);
        $operation->setFileInfo(
            [
                'file_name' => 'product.csv',
                'server_type' => 'file',
                'file_path' => 'dev/tests/integration/testsuite/Magento/ScheduledImportExport/_files',
            ]
        );

        $model->runSchedule($operation);

        $product = $productModel->loadByAttribute('sku', 'product_100500');
        $this->assertNotEmpty($product);
    }

    /**
     * teardown
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
