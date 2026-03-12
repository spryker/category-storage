<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CategoryStorage\Business\Writer;

use ArrayObject;
use Generated\Shared\Transfer\CategoryNodeCriteriaTransfer;
use Generated\Shared\Transfer\CategoryTreeStorageTransfer;
use Spryker\Zed\CategoryStorage\Business\Extractor\CategoryNodeExtractorInterface;
use Spryker\Zed\CategoryStorage\Business\TreeBuilder\CategoryStorageNodeTreeBuilderInterface;
use Spryker\Zed\CategoryStorage\Dependency\Facade\CategoryStorageToCategoryFacadeInterface;
use Spryker\Zed\CategoryStorage\Dependency\Facade\CategoryStorageToStoreFacadeInterface;
use Spryker\Zed\CategoryStorage\Persistence\CategoryStorageEntityManagerInterface;

class CategoryTreeStorageWriter implements CategoryTreeStorageWriterInterface
{
    /**
     * @var \Spryker\Zed\CategoryStorage\Persistence\CategoryStorageEntityManagerInterface
     */
    protected $categoryStorageEntityManager;

    /**
     * @var \Spryker\Zed\CategoryStorage\Business\TreeBuilder\CategoryStorageNodeTreeBuilderInterface
     */
    protected $categoryStorageNodeTreeBuilder;

    /**
     * @var \Spryker\Zed\CategoryStorage\Dependency\Facade\CategoryStorageToCategoryFacadeInterface
     */
    protected $categoryFacade;

    /**
     * @var \Spryker\Zed\CategoryStorage\Business\Extractor\CategoryNodeExtractorInterface
     */
    protected $categoryNodeExtractor;

    protected CategoryStorageToStoreFacadeInterface $storeFacade;

    public function __construct(
        CategoryStorageEntityManagerInterface $categoryStorageEntityManager,
        CategoryStorageNodeTreeBuilderInterface $categoryStorageNodeTreeBuilder,
        CategoryStorageToCategoryFacadeInterface $categoryFacade,
        CategoryNodeExtractorInterface $categoryNodeExtractor,
        CategoryStorageToStoreFacadeInterface $storeFacade
    ) {
        $this->categoryStorageEntityManager = $categoryStorageEntityManager;
        $this->categoryStorageNodeTreeBuilder = $categoryStorageNodeTreeBuilder;
        $this->categoryFacade = $categoryFacade;
        $this->categoryNodeExtractor = $categoryNodeExtractor;
        $this->storeFacade = $storeFacade;
    }

    public function writeCategoryTreeStorageCollection(): void
    {
        $localeNameMapByStoreName = $this->getLocaleNameMapByStoreName();

        $categoryNodeCriteriaTransfer = (new CategoryNodeCriteriaTransfer())
            ->setIsRoot(true)
            ->setWithRelations(true);

        $nodeCollectionTransfer = $this->categoryFacade->getCategoryNodes($categoryNodeCriteriaTransfer);

        if (!$nodeCollectionTransfer->getNodes()->count()) {
            return;
        }

        $categoryNodeIds = $this->categoryNodeExtractor->extractCategoryNodeIdsFromNodeCollection($nodeCollectionTransfer);

        $categoryNodeCriteriaTransfer = (new CategoryNodeCriteriaTransfer())
            ->setIsActive(true)
            ->setIsInMenu(true)
            ->setCategoryNodeIds($categoryNodeIds);

        $categoryNodeTransfers = $this->categoryFacade
            ->getCategoryNodesWithRelativeNodes($categoryNodeCriteriaTransfer)
            ->getNodes()
            ->getArrayCopy();

        foreach ($localeNameMapByStoreName as $storeName => $localeNames) {
            foreach ($localeNames as $localeName) {
                $categoryNodeStorageTransfers = $this->categoryStorageNodeTreeBuilder->buildCategoryNodeStorageTransfer(
                    $categoryNodeIds,
                    $categoryNodeTransfers,
                    $storeName,
                    $localeName,
                );

                $this->storeDataSet($categoryNodeStorageTransfers, $storeName, $localeName);
            }
        }
    }

    /**
     * @param array<\Generated\Shared\Transfer\CategoryNodeStorageTransfer> $categoryNodeStorageTransfers
     * @param string $storeName
     * @param string $localeName
     *
     * @return void
     */
    protected function storeDataSet(
        array $categoryNodeStorageTransfers,
        string $storeName,
        string $localeName
    ): void {
        $categoryNodeStorages = new ArrayObject();

        if ($categoryNodeStorageTransfers !== []) {
            $categoryNodeStorages = $categoryNodeStorageTransfers[array_key_first($categoryNodeStorageTransfers)]->getChildren();
        }

        $categoryTreeStorageTransfer = (new CategoryTreeStorageTransfer())
            ->setCategoryNodesStorage($categoryNodeStorages)
            ->setLocale($localeName)
            ->setStore($storeName);

        $this->categoryStorageEntityManager->saveCategoryTreeStorage($categoryTreeStorageTransfer);
    }

    /**
     * @return array<string, array<string>>
     */
    protected function getLocaleNameMapByStoreName(): array
    {
        $localeNameMapByStoreName = [];
        foreach ($this->storeFacade->getAllStores() as $storeTransfer) {
            $localeNameMapByStoreName[$storeTransfer->getName()] = $storeTransfer->getAvailableLocaleIsoCodes();
        }

        return $localeNameMapByStoreName;
    }
}
