<?php
/***
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Logging\Controller\Adminhtml\Logging;

class IndexTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    protected function setUp(): void
    {
        $this->resource = 'Magento_Logging::magento_logging_events';
        $this->uri = 'backend/admin/logging/index';
        parent::setUp();
    }
}
