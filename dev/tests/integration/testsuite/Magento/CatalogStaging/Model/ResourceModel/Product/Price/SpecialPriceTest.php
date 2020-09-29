<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogStaging\Model\ResourceModel\Product\Price;

use Magento\Catalog\Api\Data\SpecialPriceInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * @magentoAppArea webapi_rest
 * @magentoDataFixture Magento/Catalog/_files/category_product.php
 * @magentoDataFixture Magento/Catalog/_files/product_special_price.php
 */
class SpecialPriceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;

    /**
     * @var SpecialPrice
     */
    private $specialPrice;

    /**
     * @var SpecialPriceInterfaceFactory
     */
    private $specialPriceFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->specialPrice = $this->objectManager->create(SpecialPrice::class);
        $this->specialPriceFactory = $this->objectManager->create(SpecialPriceInterfaceFactory::class);
        $this->productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
    }

    /**
     * @dataProvider getDataProvider
     * @param string[] $skus
     * @param int $count
     * @return void
     */
    public function testGet(array $skus, int $count)
    {
        $pricesData = $this->specialPrice->get($skus);
        $this->assertCount($count, $pricesData);
    }

    /**
     * @return array
     */
    public function testUpdate(): array
    {
        $skus = ['simple', 'simple333'];
        $updateDatetime = new \DateTime();
        $priceFrom = $updateDatetime->modify('+10 days')
            ->format('Y-m-d H:i:s');
        $priceTo = $updateDatetime->modify('+2 days')
            ->format('Y-m-d H:i:s');

        $prices = [];
        foreach ($skus as $sku) {
            $prices[] = $this->specialPriceFactory->create()
                ->setSku($sku)
                ->setStoreId(0)
                ->setPrice(8)
                ->setPriceFrom($priceFrom)
                ->setPriceTo($priceTo);
        }
        $result = $this->specialPrice->update($prices);
        $this->assertTrue($result);
        $pricesData = $this->specialPrice->get($skus);
        $this->assertCount(4, $pricesData);

        return $skus;
    }

    /**
     * @depends testUpdate
     * @param array $skus
     * @return void
     */
    public function testDelete(array $skus)
    {
        $pricesData = $this->specialPrice->get($skus);

        $prices = [];
        foreach ($pricesData as $priceData) {
            $prices[] = $this->specialPriceFactory->create()
                ->setSku($priceData['sku'])
                ->setStoreId($priceData['store_id'])
                ->setPrice($priceData['value'])
                ->setPriceFrom($priceData['price_from'])
                ->setPriceTo($priceData['price_to']);
        }
        $result = $this->specialPrice->delete($prices);
        $this->assertTrue($result);
        $pricesData = $this->specialPrice->get($skus);
        $this->assertEmpty($pricesData);

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->get('simple');
        $this->assertEmpty($product->getSpecialPrice());
    }

    /**
     * @return array
     */
    public function getDataProvider(): array
    {
        return [
            [
                ['simple'],
                1,
            ],
            [
                ['simple333'],
                0,
            ],
            [
                ['simple', 'simple333'],
                1,
            ],
        ];
    }
}
