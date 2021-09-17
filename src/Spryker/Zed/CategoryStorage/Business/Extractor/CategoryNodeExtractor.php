<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CategoryStorage\Business\Extractor;

use Generated\Shared\Transfer\NodeCollectionTransfer;

class CategoryNodeExtractor implements CategoryNodeExtractorInterface
{
    /**
     * @param \Generated\Shared\Transfer\NodeCollectionTransfer $nodeCollectionTransfer
     *
     * @return array<int>
     */
    public function extractCategoryNodeIdsFromNodeCollection(NodeCollectionTransfer $nodeCollectionTransfer): array
    {
        $categoryNodeIds = [];

        foreach ($nodeCollectionTransfer->getNodes() as $nodeTransfer) {
            $categoryNodeIds[] = $nodeTransfer->getIdCategoryNodeOrFail();
        }

        return $categoryNodeIds;
    }
}
