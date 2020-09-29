<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml;

/**
 * @magentoAppArea adminhtml
 */
class IndexTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    public function testLoadBlockAction()
    {
        $this->getRequest()->setParam('block', ',');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/checkout/index/loadBlock');
        $this->assertStringMatchesFormat(
            '{"message":"%AThis customer couldn\'t be found. Verify the customer and try again.%A"}',
            $this->getResponse()->getBody()
        );
    }
}
