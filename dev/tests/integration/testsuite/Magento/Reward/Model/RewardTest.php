<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Reward\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;

/**
 *  Reward points balance email notification test
 */
class RewardTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Reward
     */
    private $model;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(Reward::class);
    }

    /**
     * Test reward update notification functionality
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSendBalanceUpdateNotification()
    {
        /** @var CustomerInterface $customerInterface */
        $customerInterface = $this->objectManager->create(CustomerInterface::class);
        $customerInterface->setId(1);
        $customerInterface->setEmail('customer@example.com');
        $customerInterface->setCustomAttribute('reward_update_notification', 0);
        $this->model->setCustomer($customerInterface);
        $this->model->setPointsDelta(100);
        $this->model->sendBalanceUpdateNotification();
        $this->assertNull($this->model->getData('balance_update_sent'));
    }
}
