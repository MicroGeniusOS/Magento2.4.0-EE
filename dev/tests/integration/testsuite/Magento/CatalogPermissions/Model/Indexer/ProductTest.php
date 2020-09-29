<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogPermissions\Model\Indexer;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\CatalogPermissions\Model\Indexer\Product as IndexerProduct;
use Magento\CatalogPermissions\Model\ResourceModel\Permission\Index;
use Magento\Indexer\Model\Indexer;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Product test
 *
 * @magentoDbIsolation disabled
 * @magentoAppIsolation enabled
 *
 * Tests how category permission affects indexation mechanism
 */
class ProductTest extends TestCase
{
    /**
     * @var Index
     */
    protected $indexTable;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->indexTable = $this->objectManager->create(Index::class);
        $this->product = $this->objectManager->create(Product::class);
    }

    /**
     * Reindex all test
     *
     * @magentoConfigFixture current_store catalog/magento_catalogpermissions/enabled 1
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/permission.php
     * @magentoDataFixture Magento/CatalogPermissions/_files/product.php
     */
    public function testReindexAll()
    {
        $product = $this->getProduct();
        /** @var  $indexer \Magento\Framework\Indexer\IndexerInterface */
        $indexer = $this->objectManager->create(Indexer::class);
        $indexer->load(IndexerProduct::INDEXER_ID);
        $indexer->reindexAll();

        $productData = array_merge(['product_id' => $product->getId()], $this->getProductData());
        $this->assertContainsEquals($productData, $this->indexTable->getIndexForProduct($product->getId(), 1, 1));

        $product->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE);
        $product->save();
        $this->assertNotEmpty($this->indexTable->getIndexForProduct($product->getId(), 1, 1));

        $product->setStatus(Status::STATUS_DISABLED);
        $product->save();
        $this->assertEmpty($this->indexTable->getIndexForProduct($product->getId(), 1, 1));
    }

    /**
     * Get product data
     *
     * @return array
     */
    protected function getProductData()
    {
        return [
            'grant_catalog_category_view' => '-2',
            'grant_catalog_product_price' => '-2',
            'grant_checkout_items' => '-2',
            'customer_group_id' => 1
        ];
    }

    /**
     * Get product
     *
     * @return Product
     */
    protected function getProduct()
    {
        return $this->product->load(150);
    }
}
