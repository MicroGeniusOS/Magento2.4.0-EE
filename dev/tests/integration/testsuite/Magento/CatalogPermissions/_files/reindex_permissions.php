<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create(\Magento\CatalogPermissions\Model\Indexer\Category::class)
    ->executeFull();

\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create(\Magento\CatalogPermissions\Model\Indexer\Product::class)
    ->executeFull();
