<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ScheduledImportExport\Cron;

use Magento\TestFramework\Helper\Bootstrap;

class ScheduledLogCleanTest extends \Magento\TestFramework\Indexer\TestCase
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

    /**
     * @codingStandardsIgnoreStart
     * @magentoConfigFixture current_store crontab/default/jobs/magento_scheduled_import_export_log_clean/schedule/cron_expr 1
     * @codingStandardsIgnoreEnd
     * @magentoDataFixture Magento/ScheduledImportExport/_files/operation.php
     * @magentoDataFixture Magento/Catalog/_files/products_new.php
     * @magentoDbIsolation disabled
     */
    public function testScheduledLogClean()
    {
        // Set up
        /** @var \Magento\ScheduledImportExport\Model\Scheduled\Operation $operation */
        $operation = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\ScheduledImportExport\Model\Scheduled\Operation::class
        );

        $operation->load('export', 'operation_type');

        $fileInfo = $operation->getFileInfo();
        $historyPath = $operation->getHistoryFilePath();

        // Create export directory if not exist
        /** @var \Magento\Framework\Filesystem\Directory\Write $varDir */
        $varDir = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\Filesystem::class
        )->getDirectoryWrite(
            'var'
        );
        $varDir->create($fileInfo['file_path']);

        // Change current working directory to allow save export results
        $cwd = getcwd();
        chdir($varDir->getAbsolutePath());

        $operation->run();

        $this->assertFileExists($historyPath);

        // Restore current working directory
        chdir($cwd);

        $observer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get(\Magento\ScheduledImportExport\Cron\ScheduledLogClean::class);
        $observer->execute(true);

        // Verify
        $this->assertFileNotExists($historyPath);
    }

    /**
     * teardown
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
