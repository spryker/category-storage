<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CategoryStorage\Business;

interface CategoryStorageFacadeInterface
{
    /**
     * Specification:
     * - Queries all category nodes with categoryNodeIds
     * - Creates a data structure tree
     * - Stores data as json encoded to storage table
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @deprecated Will be removed in the next major without replacement.
     *
     * @param array<int> $categoryNodeIds
     *
     * @return void
     */
    public function publish(array $categoryNodeIds): void;

    /**
     * Specification:
     * - Finds and deletes category node storage entities with categoryNodeIds
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @deprecated Will be removed in the next major without replacement.
     *
     * @param array<int> $categoryNodeIds
     *
     * @return void
     */
    public function unpublish(array $categoryNodeIds): void;

    /**
     * Specification:
     * - Queries all categories
     * - Creates a data structure category tree
     * - Stores data as json encoded to storage table
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\CategoryStorage\Business\CategoryStorageFacadeInterface::writeCategoryTreeStorageCollection} instead.
     *
     * @return void
     */
    public function publishCategoryTree(): void;

    /**
     * Specification:
     * - Finds and deletes all category tree storage entities
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\CategoryStorage\Business\CategoryStorageFacadeInterface::deleteCategoryTreeStorageCollection()} instead.
     *
     * @return void
     */
    public function unpublishCategoryTree(): void;

    /**
     * Specification:
     * - Extracts category IDs from the $eventTransfers created by category store entity events.
     * - Finds all category node IDs related to category IDs.
     * - Queries all category nodes with category node IDs.
     * - Creates a data structure tree.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCategoryNodeStorageCollectionByCategoryStoreEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category IDs from the $eventTransfers created by category store publish events.
     * - Finds all category node IDs related to category IDs.
     * - Queries all category nodes with category node IDs.
     * - Creates a data structure tree.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCategoryNodeStorageCollectionByCategoryStorePublishEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Queries all categories.
     * - Creates a data structure category tree.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @return void
     */
    public function writeCategoryTreeStorageCollection(): void;

    /**
     * Specification:
     * - Finds and deletes all category tree storage entities.
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @return void
     */
    public function deleteCategoryTreeStorageCollection(): void;

    /**
     * Specification:
     * - Retrieves a category node storage collection according to provided offset, limit and categoryNodeIds.
     *
     * @api
     *
     * @param int $offset
     * @param int $limit
     * @param array<int> $categoryNodeIds
     *
     * @return array<\Generated\Shared\Transfer\SynchronizationDataTransfer>
     */
    public function getCategoryNodeStorageSynchronizationDataTransfersByCategoryNodeIds(int $offset, int $limit, array $categoryNodeIds): array;

    /**
     * Specification:
     * - Retrieves a category tree storage collection according to provided offset, limit and `categoryTreeStorageIds`.
     *
     * @api
     *
     * @param int $offset
     * @param int $limit
     * @param array<int> $categoryTreeStorageIds
     *
     * @return array<\Generated\Shared\Transfer\SynchronizationDataTransfer>
     */
    public function getCategoryTreeStorageSynchronizationDataTransfersByCategoryTreeStorageIds(int $offset, int $limit, array $categoryTreeStorageIds): array;

    /**
     * Specification:
     * - Extracts category IDs from the $eventTransfers created by category attribute entity events.
     * - Finds all category node IDs related to category IDs.
     * - Queries all category nodes with category node IDs.
     * - Creates a data structure tree.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCategoryNodeStorageCollectionByCategoryAttributeEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category IDs from the $eventTransfers created by category entity events.
     * - Finds all category node IDs related to category IDs.
     * - Queries all category nodes with category node IDs.
     * - Creates a data structure tree.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCategoryNodeStorageCollectionByCategoryEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category template IDs from the $eventTransfers created by category template events.
     * - Finds all category node IDs related to category template IDs.
     * - Queries all category nodes with category node IDs.
     * - Creates a data structure tree.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCategoryNodeStorageCollectionByCategoryTemplateEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category node IDs from the $eventTransfers created by parent category entity events.
     * - Creates a data structure tree.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCategoryNodeStorageCollectionByParentCategoryEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category node IDs from the $eventTransfers created by category node entity events.
     * - Creates a data structure tree.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCategoryNodeStorageCollectionByCategoryNodeEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category IDs from the $eventTransfers created by category entity events.
     * - Finds all category node IDs related to category IDs.
     * - Deletes category node storage entities with category node IDs.
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function deleteCategoryNodeStorageCollectionByCategoryEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category IDs from the $eventTransfers created by category attribute entity events.
     * - Finds all category node IDs related to category IDs.
     * - Deletes category node storage entities with category node IDs.
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function deleteCategoryNodeStorageCollectionByCategoryAttributeEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category template IDs from the $eventTransfers created by category template events.
     * - Finds all category node IDs related to category template IDs.
     * - Deletes category node storage entities with category node IDs.
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function deleteCategoryNodeStorageCollectionByCategoryTemplateEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category node IDs from the $eventTransfers created by category node entity events.
     * - Deletes category node storage entities with category node IDs.
     * - Sends a copy of data to the queue.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function deleteCategoryNodeStorageCollectionByCategoryNodeEvents(array $eventEntityTransfers): void;
}
