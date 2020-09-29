<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry */
$indexerRegistry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Framework\Indexer\IndexerRegistry::class
);
$indexer =  $indexerRegistry->get('catalogsearch_fulltext');
$indexer->reindexAll();

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Product::class);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setAttributeSetId(
    4
)->setWebsiteIds(
    [1]
)->setName(
    'Simple Product 1'
)->setSku(
    'simple'
)->setPrice(
    10
)->setQty(
    100
)->setDescription(
    'Description with <b>html tag</b>'
)->setMetaTitle(
    'meta title'
)->setMetaKeyword(
    'meta keyword'
)->setMetaDescription(
    'meta description'
)->setVisibility(
    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
)->setStatus(
    \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
)->setCategoryIds(
    [2]
)->setStockData(
    ['use_config_manage_stock' => 0]
)->setCanSaveCustomOptions(
    true
)->setHasOptions(
    true
)->save();
