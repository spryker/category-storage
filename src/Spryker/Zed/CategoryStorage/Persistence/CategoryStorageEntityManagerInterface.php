<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CategoryStorage\Persistence;

use Generated\Shared\Transfer\CategoryNodeStorageTransfer;
use Generated\Shared\Transfer\CategoryTreeStorageTransfer;

interface CategoryStorageEntityManagerInterface
{
    public function saveCategoryNodeStorageForStoreAndLocale(
        CategoryNodeStorageTransfer $categoryNodeStorageTransfer,
        string $storeName,
        string $localeName
    ): void;

    /**
     * @param array<int> $categoryNodeIds
     * @param string $localeName
     * @param string $storeName
     *
     * @return void
     */
    public function deleteCategoryNodeStoragesForStoreAndLocale(array $categoryNodeIds, string $localeName, string $storeName): void;

    /**
     * @param array<int> $categoryNodeIds
     *
     * @return void
     */
    public function deleteCategoryNodeStorageByCategoryNodeIds(array $categoryNodeIds): void;

    public function saveCategoryTreeStorage(CategoryTreeStorageTransfer $categoryTreeStorageTransfer): void;

    public function deleteCategoryTreeStorageCollection(): void;
}
