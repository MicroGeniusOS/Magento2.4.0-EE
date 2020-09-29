<?php
/***
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class RemoveTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Magento_SalesArchive::remove';
        $this->uri = 'backend/sales/archive/remove';
        parent::setUp();
    }
}
