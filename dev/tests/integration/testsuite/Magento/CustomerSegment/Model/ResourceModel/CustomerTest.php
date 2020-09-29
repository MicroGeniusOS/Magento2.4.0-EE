<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CustomerSegment\Model\ResourceModel;

use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    /**
     * Tests customer segments for a website with account sharing enabled
     *
     * @magentoDataFixture Magento/CustomerSegment/_files/segment_multiwebsite.php
     * @magentoConfigFixture current_store customer/account_share/scope 0
     */
    public function testGetCustomerWebsiteSegmentsAccountSharingEnabled()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Website $mainWebsite */
        /** @var Website $secondWebsite */
        $mainWebsite = $objectManager->create(Website::class)->load('base');
        $secondWebsite = $objectManager->create(Website::class)->load('secondwebsite');
        $customerModel = $objectManager->create(\Magento\Customer\Model\Customer::class);
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $customerModel->loadByEmail('customer@example.com');
        $segmentModel = $objectManager->create(\Magento\CustomerSegment\Model\Segment::class);
        /** @var Customer $customerSegmentResourceModel */
        $customerSegmentResourceModel = $objectManager->create(Customer::class);
        /** @var $segment \Magento\CustomerSegment\Model\Segment */
        $segment = $segmentModel->load('Customer Segment Multi-Website', 'name');
        $this->assertEquals(
            [$segment->getId()],
            $customerSegmentResourceModel->getCustomerWebsiteSegments($customer->getId(), $mainWebsite->getId())
        );
        $this->assertEquals(
            [$segment->getId()],
            $customerSegmentResourceModel->getCustomerWebsiteSegments($customer->getId(), $secondWebsite->getId())
        );
    }

    /**
     * Tests customer segments for a website with account sharing disabled
     *
     * @magentoDataFixture Magento/CustomerSegment/_files/segment_multiwebsite.php
     * @magentoConfigFixture current_store customer/account_share/scope 1
     */
    public function testGetCustomerWebsiteSegmentsWithAccountSharingDisabled()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Website $mainWebsite */
        /** @var Website $secondWebsite */
        $mainWebsite = $objectManager->create(Website::class)->load('base');
        $secondWebsite = $objectManager->create(Website::class)->load('secondwebsite');
        $customerModel = $objectManager->create(\Magento\Customer\Model\Customer::class);
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $customerModel->setWebsiteId($mainWebsite->getId())->loadByEmail('customer@example.com');
        $segmentModel = $objectManager->create(\Magento\CustomerSegment\Model\Segment::class);
        /** @var Customer $customerSegmentResourceModel */
        $customerSegmentResourceModel = $objectManager->create(Customer::class);
        /** @var $segment \Magento\CustomerSegment\Model\Segment */
        $segment = $segmentModel->load('Customer Segment Multi-Website', 'name');
        $this->assertEquals(
            [$segment->getId()],
            $customerSegmentResourceModel->getCustomerWebsiteSegments($customer->getId(), $mainWebsite->getId())
        );
        $this->assertEquals(
            [],
            $customerSegmentResourceModel->getCustomerWebsiteSegments($customer->getId(), $secondWebsite->getId())
        );
    }
}
