<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\CategoryStorage\Formatter;

use ArrayObject;
use Spryker\Client\CategoryStorage\Mapper\CategoryNodeStorageMapperInterface;
use Spryker\Client\CategoryStorage\Storage\CategoryTreeStorageReaderInterface;

class CategoryTreeFilterFormatter implements CategoryTreeFilterFormatterInterface
{
    /**
     * @uses \Spryker\Client\SearchElasticsearch\AggregationExtractor\CategoryExtractor::DOC_COUNT
     *
     * @var string
     */
    protected const DOC_COUNT = 'doc_count';

    /**
     * @uses \Spryker\Client\SearchElasticsearch\AggregationExtractor\CategoryExtractor::KEY_BUCKETS
     *
     * @var string
     */
    protected const KEY_BUCKETS = 'buckets';

    /**
     * @uses \Spryker\Client\SearchElasticsearch\AggregationExtractor\CategoryExtractor::KEY_KEY
     *
     * @var string
     */
    protected const KEY_KEY = 'key';

    /**
     * @var \Spryker\Client\CategoryStorage\Storage\CategoryTreeStorageReaderInterface
     */
    protected $categoryTreeStorageReader;

    /**
     * @var \Spryker\Client\CategoryStorage\Mapper\CategoryNodeStorageMapperInterface
     */
    protected $categoryNodeStorageMapper;

    /**
     * @param \Spryker\Client\CategoryStorage\Storage\CategoryTreeStorageReaderInterface $categoryTreeStorageReader
     * @param \Spryker\Client\CategoryStorage\Mapper\CategoryNodeStorageMapperInterface $categoryNodeStorageMapper
     */
    public function __construct(
        CategoryTreeStorageReaderInterface $categoryTreeStorageReader,
        CategoryNodeStorageMapperInterface $categoryNodeStorageMapper
    ) {
        $this->categoryTreeStorageReader = $categoryTreeStorageReader;
        $this->categoryNodeStorageMapper = $categoryNodeStorageMapper;
    }

    /**
     * @param array $docCountAggregation
     * @param string $localeName
     * @param string $storeName
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\CategoryNodeSearchResultTransfer>
     */
    public function formatCategoryTreeFilter(array $docCountAggregation, string $localeName, string $storeName): ArrayObject
    {
        $categoryDocCounts = $this->getMappedCategoryDocCountsByNodeId($docCountAggregation);

        $categoryNodeStorageTransfers = $this->categoryTreeStorageReader->getCategories($localeName, $storeName);
        $categoryNodeSearchResultTransfers = $this->categoryNodeStorageMapper->mapCategoryNodeStoragesToCategoryNodeSearchResults(
            $categoryNodeStorageTransfers,
            new ArrayObject(),
        );

        return $this->mergeCategoryNodeSearchResultWithCategoryDocCount(
            $categoryNodeSearchResultTransfers,
            $categoryDocCounts,
        );
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\CategoryNodeSearchResultTransfer> $categoryNodeSearchResultTransfers
     * @param array $categoryDocCounts
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\CategoryNodeSearchResultTransfer>
     */
    protected function mergeCategoryNodeSearchResultWithCategoryDocCount(
        ArrayObject $categoryNodeSearchResultTransfers,
        array $categoryDocCounts
    ): ArrayObject {
        foreach ($categoryNodeSearchResultTransfers as $categoryNodeSearchResultTransfer) {
            $docCount = $categoryDocCounts[$categoryNodeSearchResultTransfer->getNodeId()] ?? 0;
            $categoryNodeSearchResultTransfer->setDocCount($docCount);

            if ($categoryNodeSearchResultTransfer->getChildren()->count()) {
                $categoryNodeSearchResultTransfer->setChildren(
                    $this->mergeCategoryNodeSearchResultWithCategoryDocCount(
                        $categoryNodeSearchResultTransfer->getChildren(),
                        $categoryDocCounts,
                    ),
                );
            }

            if ($categoryNodeSearchResultTransfer->getParents()->count()) {
                $categoryNodeSearchResultTransfer->setParents(
                    $this->mergeCategoryNodeSearchResultWithCategoryDocCount(
                        $categoryNodeSearchResultTransfer->getParents(),
                        $categoryDocCounts,
                    ),
                );
            }
        }

        return $categoryNodeSearchResultTransfers;
    }

    /**
     * @param array $docCountAggregation
     *
     * @return array<int>
     */
    protected function getMappedCategoryDocCountsByNodeId(array $docCountAggregation): array
    {
        $categoryDocCounts = [];
        $categoryBuckets = $docCountAggregation[static::KEY_BUCKETS] ?? [];

        foreach ($categoryBuckets as $categoryBucket) {
            $key = $categoryBucket[static::KEY_KEY] ?? null;
            $docCount = $categoryBucket[static::DOC_COUNT] ?? null;

            if ($key && $docCount) {
                $categoryDocCounts[$key] = $docCount;
            }
        }

        return $categoryDocCounts;
    }
}
