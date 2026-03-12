<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CategoryStorage\Business\Deleter;

use Generated\Shared\Transfer\CategoryNodeCriteriaTransfer;

interface CategoryNodeStorageDeleterInterface
{
    /**
     * @param array<array<array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer>>> $categoryNodeStorageTransferTreesIndexedByLocaleAndStore
     * @param array<int> $categoryNodeIds
     *
     * @return void
     */
    public function deleteMissingCategoryNodeStorage(array $categoryNodeStorageTransferTreesIndexedByLocaleAndStore, array $categoryNodeIds): void;

    /**
     * @param array<int> $categoryNodeIds
     *
     * @return void
     */
    public function deleteCategoryNodeStorageCollection(array $categoryNodeIds): void;

    /**
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function deleteCategoryNodeStorageCollectionByCategoryNodeEvents(array $eventEntityTransfers): void;

    public function deleteCategoryNodeStorageCollectionByCategoryNodeCriteria(CategoryNodeCriteriaTransfer $categoryNodeCriteriaTransfer): void;

    /**
     * @param array<int> $categoryNodeIds
     * @param array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer> $categoryNodeStorageTransfers
     * @param string $localeName
     * @param string $storeName
     *
     * @return void
     */
    public function deleteMissingCategoryNodeStorageForLocaleAndStore(
        array $categoryNodeIds,
        array $categoryNodeStorageTransfers,
        string $localeName,
        string $storeName
    ): void;
}
