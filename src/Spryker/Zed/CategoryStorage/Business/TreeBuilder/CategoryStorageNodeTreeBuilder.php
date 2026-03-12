<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CategoryStorage\Business\TreeBuilder;

use Generated\Shared\Transfer\CategoryNodeStorageTransfer;
use Spryker\Zed\CategoryStorage\Business\Mapper\CategoryNodeStorageMapperInterface;
use Spryker\Zed\CategoryStorage\Dependency\Facade\CategoryStorageToStoreFacadeInterface;

class CategoryStorageNodeTreeBuilder implements CategoryStorageNodeTreeBuilderInterface
{
    /**
     * @var \Spryker\Zed\CategoryStorage\Dependency\Facade\CategoryStorageToStoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @var \Spryker\Zed\CategoryStorage\Business\Mapper\CategoryNodeStorageMapperInterface
     */
    protected $categoryNodeStorageMapper;

    /**
     * @var array<int, array<int>>
     */
    protected array $childrenIndexCache = [];

    public function __construct(
        CategoryStorageToStoreFacadeInterface $storeFacade,
        CategoryNodeStorageMapperInterface $categoryNodeStorageMapper
    ) {
        $this->storeFacade = $storeFacade;
        $this->categoryNodeStorageMapper = $categoryNodeStorageMapper;
    }

    /**
     * @param array<int> $categoryNodeIds
     * @param array<\Generated\Shared\Transfer\NodeTransfer> $nodeTransfers
     * @param string $storeName
     * @param string $localeName
     *
     * @return array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer>
     */
    public function buildCategoryNodeStorageTransfer(array $categoryNodeIds, array $nodeTransfers, string $storeName, string $localeName): array
    {
        $indexedNodeTransfers = $this->indexCategoryNodesByIdCategoryNode($nodeTransfers);

        if (!$this->childrenIndexCache) {
            $this->childrenIndexCache = $this->buildChildrenIndex($indexedNodeTransfers);
        }

        $categoryNodeStorageTransfers = $this->categoryNodeStorageMapper->mapNodeTransfersToCategoryNodeStorageTransfersByLocaleAndStore(
            $indexedNodeTransfers,
            $localeName,
            $storeName,
        );

        return $this->buildCategoryNodeStorageTransferTrees(
            $categoryNodeIds,
            $indexedNodeTransfers,
            $categoryNodeStorageTransfers,
        );
    }

    /**
     * @param array<\Generated\Shared\Transfer\NodeTransfer> $nodeTransfers
     *
     * @return array<\Generated\Shared\Transfer\NodeTransfer>
     */
    protected function indexCategoryNodesByIdCategoryNode(array $nodeTransfers): array
    {
        $indexedNodeTransfers = [];

        foreach ($nodeTransfers as $nodeTransfer) {
            $indexedNodeTransfers[$nodeTransfer->getIdCategoryNodeOrFail()] = $nodeTransfer;
        }

        return $indexedNodeTransfers;
    }

    /**
     * @param array<\Generated\Shared\Transfer\NodeTransfer> $indexedNodeTransfers
     *
     * @return array<int, array<int>>
     */
    protected function buildChildrenIndex(array $indexedNodeTransfers): array
    {
        $childrenIndex = [];

        foreach ($indexedNodeTransfers as $nodeTransfer) {
            $parentId = $nodeTransfer->getFkParentCategoryNode();
            if ($parentId === null) {
                continue;
            }

            if (!isset($childrenIndex[$parentId])) {
                $childrenIndex[$parentId] = [];
            }

            $childrenIndex[$parentId][] = $nodeTransfer->getIdCategoryNodeOrFail();
        }

        return $childrenIndex;
    }

    /**
     * @return array<array<string>>
     */
    protected function getLocaleNameMapByStoreName(): array
    {
        $localeNameMapByStoreName = [];
        foreach ($this->storeFacade->getAllStores() as $storeTransfer) {
            $localeNameMapByStoreName[$storeTransfer->getName()] = $storeTransfer->getAvailableLocaleIsoCodes();
        }

        return $localeNameMapByStoreName;
    }

    /**
     * @param array<int> $categoryNodeIds
     * @param array<\Generated\Shared\Transfer\NodeTransfer> $indexedNodeTransfers
     * @param array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer> $indexedCategoryNodeStorageTransfers
     *
     * @return array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer>
     */
    protected function buildCategoryNodeStorageTransferTrees(
        array $categoryNodeIds,
        array $indexedNodeTransfers,
        array $indexedCategoryNodeStorageTransfers
    ): array {
        $categoryNodeStorageTransferTrees = [];
        foreach ($categoryNodeIds as $idCategoryNode) {
            if (!isset($indexedCategoryNodeStorageTransfers[$idCategoryNode])) {
                continue;
            }

            $categoryNodeStorageTransfer = $this->cloneCategoryNodeStorageTransfer($indexedCategoryNodeStorageTransfers[$idCategoryNode]);

            if (!$categoryNodeStorageTransfer->getIsActive()) {
                continue;
            }

            $categoryNodeStorageTransfer = $this->buildChildrenTree(
                $categoryNodeStorageTransfer,
                $indexedNodeTransfers,
                $indexedCategoryNodeStorageTransfers,
            );
            $categoryNodeStorageTransfer = $this->buildParentsTree(
                $categoryNodeStorageTransfer,
                $indexedNodeTransfers,
                $indexedCategoryNodeStorageTransfers,
            );

            $categoryNodeStorageTransferTrees[$idCategoryNode] = $categoryNodeStorageTransfer;
        }

        return $categoryNodeStorageTransferTrees;
    }

    /**
     * @param \Generated\Shared\Transfer\CategoryNodeStorageTransfer $categoryNodeStorageTransfer
     * @param array<\Generated\Shared\Transfer\NodeTransfer> $indexedNodeTransfers
     * @param array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer> $indexedCategoryNodeStorageTransfers
     *
     * @return \Generated\Shared\Transfer\CategoryNodeStorageTransfer
     */
    protected function buildChildrenTree(
        CategoryNodeStorageTransfer $categoryNodeStorageTransfer,
        array $indexedNodeTransfers,
        array $indexedCategoryNodeStorageTransfers
    ): CategoryNodeStorageTransfer {
        $childrenCategoryNodeStorageTransfers = $this->findChildren(
            $categoryNodeStorageTransfer->getNodeIdOrFail(),
            $indexedNodeTransfers,
            $indexedCategoryNodeStorageTransfers,
        );
        foreach ($childrenCategoryNodeStorageTransfers as $childrenCategoryNodeStorageTransfer) {
            $childrenCategoryNodeStorageTransfer = $this->buildChildrenTree(
                $childrenCategoryNodeStorageTransfer,
                $indexedNodeTransfers,
                $indexedCategoryNodeStorageTransfers,
            );
            $categoryNodeStorageTransfer->addChildren($childrenCategoryNodeStorageTransfer);
        }

        return $categoryNodeStorageTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CategoryNodeStorageTransfer $categoryNodeStorageTransfer
     * @param array<\Generated\Shared\Transfer\NodeTransfer> $indexedNodeTransfers
     * @param array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer> $indexedCategoryNodeStorageTransfers
     *
     * @return \Generated\Shared\Transfer\CategoryNodeStorageTransfer
     */
    protected function buildParentsTree(
        CategoryNodeStorageTransfer $categoryNodeStorageTransfer,
        array $indexedNodeTransfers,
        array $indexedCategoryNodeStorageTransfers
    ): CategoryNodeStorageTransfer {
        $nodeTransfer = $indexedNodeTransfers[$categoryNodeStorageTransfer->getNodeId()] ?? null;
        if (!$nodeTransfer || !$nodeTransfer->getFkParentCategoryNode()) {
            return $categoryNodeStorageTransfer;
        }

        $parentCategoryNodeStorageTransfers = $this->findParents(
            $nodeTransfer->getFkParentCategoryNodeOrFail(),
            $indexedCategoryNodeStorageTransfers,
        );
        foreach ($parentCategoryNodeStorageTransfers as $parentCategoryNodeStorageTransfer) {
            $parentCategoryNodeStorageTransfer = $this->buildParentsTree(
                $parentCategoryNodeStorageTransfer,
                $indexedNodeTransfers,
                $indexedCategoryNodeStorageTransfers,
            );
            $categoryNodeStorageTransfer->addParents($parentCategoryNodeStorageTransfer);
        }

        return $categoryNodeStorageTransfer;
    }

    /**
     * Optimized O(1) lookup using pre-built children index instead of O(n) iteration.
     *
     * @param int $idCategoryNode
     * @param array<\Generated\Shared\Transfer\NodeTransfer> $indexedNodeTransfers
     * @param array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer> $indexedCategoryNodeStorageTransfers
     *
     * @return array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer>
     */
    protected function findChildren(int $idCategoryNode, array $indexedNodeTransfers, array $indexedCategoryNodeStorageTransfers): array
    {
        $childrenCategoryNodeStorageTransfers = [];

        $childNodeIds = $this->childrenIndexCache[$idCategoryNode] ?? [];

        foreach ($childNodeIds as $childNodeId) {
            if (isset($indexedCategoryNodeStorageTransfers[$childNodeId])) {
                $childrenCategoryNodeStorageTransfers[] = $this->cloneCategoryNodeStorageTransfer(
                    $indexedCategoryNodeStorageTransfers[$childNodeId],
                );
            }
        }

        return $childrenCategoryNodeStorageTransfers;
    }

    /**
     * @param int $idParentCategoryNode
     * @param array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer> $indexedCategoryNodeStorageTransfers
     *
     * @return array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer>
     */
    protected function findParents(int $idParentCategoryNode, array $indexedCategoryNodeStorageTransfers): array
    {
        if (!isset($indexedCategoryNodeStorageTransfers[$idParentCategoryNode])) {
            return [];
        }

        return [$this->cloneCategoryNodeStorageTransfer($indexedCategoryNodeStorageTransfers[$idParentCategoryNode])];
    }

    protected function cloneCategoryNodeStorageTransfer(CategoryNodeStorageTransfer $categoryNodeStorageTransfer): CategoryNodeStorageTransfer
    {
        return (new CategoryNodeStorageTransfer())->fromArray($categoryNodeStorageTransfer->toArray(), true);
    }
}
