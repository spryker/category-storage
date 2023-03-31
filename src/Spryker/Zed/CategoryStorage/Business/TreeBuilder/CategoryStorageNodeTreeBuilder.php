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
     * @param \Spryker\Zed\CategoryStorage\Dependency\Facade\CategoryStorageToStoreFacadeInterface $storeFacade
     * @param \Spryker\Zed\CategoryStorage\Business\Mapper\CategoryNodeStorageMapperInterface $categoryNodeStorageMapper
     */
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
     *
     * @return array<array<array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer>>>
     */
    public function buildCategoryNodeStorageTransferTreesForLocaleAndStore(array $categoryNodeIds, array $nodeTransfers): array
    {
        $localeNameMapByStoreName = $this->getLocaleNameMapByStoreName();
        $indexedNodeTransfers = $this->indexCategoryNodesByIdCategoryNode($nodeTransfers);

        $categoryNodeStorageTransferTrees = [];
        foreach ($localeNameMapByStoreName as $storeName => $localeNames) {
            foreach ($localeNames as $localeName) {
                $categoryNodeStorageTransfers = $this->categoryNodeStorageMapper->mapNodeTransfersToCategoryNodeStorageTransfersByLocaleAndStore(
                    $indexedNodeTransfers,
                    $localeName,
                    $storeName,
                );

                $categoryNodeStorageTransferTrees[$storeName][$localeName] = $this->buildCategoryNodeStorageTransferTrees(
                    $categoryNodeIds,
                    $indexedNodeTransfers,
                    $categoryNodeStorageTransfers,
                );
            }
        }

        return $categoryNodeStorageTransferTrees;
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
     * @param int $idCategoryNode
     * @param array<\Generated\Shared\Transfer\NodeTransfer> $indexedNodeTransfers
     * @param array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer> $indexedCategoryNodeStorageTransfers
     *
     * @return array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer>
     */
    protected function findChildren(int $idCategoryNode, array $indexedNodeTransfers, array $indexedCategoryNodeStorageTransfers): array
    {
        $childrenCategoryNodeStorageTransfers = [];
        foreach ($indexedNodeTransfers as $nodeTransfer) {
            if ($idCategoryNode === $nodeTransfer->getFkParentCategoryNode() && isset($indexedCategoryNodeStorageTransfers[$nodeTransfer->getIdCategoryNode()])) {
                $childrenCategoryNodeStorageTransfers[] = $this->cloneCategoryNodeStorageTransfer(
                    $indexedCategoryNodeStorageTransfers[$nodeTransfer->getIdCategoryNode()],
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
        $parentCategoryNodeStorageTransfers = [];
        foreach ($indexedCategoryNodeStorageTransfers as $idCategoryNode => $categoryNodeStorageTransfer) {
            if ($idParentCategoryNode === $idCategoryNode) {
                $parentCategoryNodeStorageTransfers[] = $this->cloneCategoryNodeStorageTransfer($categoryNodeStorageTransfer);
            }
        }

        return $parentCategoryNodeStorageTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\CategoryNodeStorageTransfer $categoryNodeStorageTransfer
     *
     * @return \Generated\Shared\Transfer\CategoryNodeStorageTransfer
     */
    protected function cloneCategoryNodeStorageTransfer(CategoryNodeStorageTransfer $categoryNodeStorageTransfer): CategoryNodeStorageTransfer
    {
        return (new CategoryNodeStorageTransfer())->fromArray($categoryNodeStorageTransfer->toArray(), true);
    }
}
