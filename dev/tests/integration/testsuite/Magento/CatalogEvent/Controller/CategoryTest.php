<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogEvent\Controller;

/**
 * @magentoAppArea adminhtml
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class CategoryTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * Covers \Magento\CatalogEvent\Block\Adminhtml\Catalog\Category\Edit\AddEventButton for Add Event button
     *
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testEditCategoryAction()
    {
        $this->dispatch('backend/catalog/category/edit/id/3');
        $this->assertStringContainsString(
            'setLocation(\'http://localhost/index.php/backend/admin/catalog_event/new/category/1/category_id/',
            $this->getResponse()->getBody()
        );
    }

    /**
     * Covers \Magento\CatalogEvent\Block\Adminhtml\Catalog\Category\Edit\AddEventButton for Edit Event button
     *
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogEvent/_files/category_event.php
     */
    public function testEditCategoryActionEditEvent()
    {
        $this->dispatch('backend/catalog/category/edit/id/3');
        $this->assertStringContainsString(
            'setLocation(\'http://localhost/index.php/backend/admin/catalog_event/edit/id/',
            $this->getResponse()->getBody()
        );
    }
}
